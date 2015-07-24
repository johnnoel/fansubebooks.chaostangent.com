<?php

namespace ChaosTangent\FansubEbooks\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use ChaosTangent\FansubEbooks\Entity\Result\SearchResult;
use ChaosTangent\FansubEbooks\Entity\Series,
    ChaosTangent\FansubEbooks\Entity\File;

/**
 * Line entity repository
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class LineRepository extends EntityRepository
{
    /**
     * Get a line with relevant vote counts
     *
     * @param integer $id
     * @return Line|null
     */
    public function getLine($id)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->addSelect([
                'SUM(CASE WHEN v.positive = true THEN 1 ELSE 0 END) AS positive_votes',
                'SUM(CASE WHEN v.positive = false THEN 1 ELSE 0 END) AS negative_votes',
            ])
            ->leftJoin('l.votes', 'v')
            ->where($qb->expr()->eq('l.id', ':id'))
            ->groupBy('l.id')
            ->setMaxResults(1)
            ->setParameter('id', $id);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result === null) {
            return $result;
        }

        $line = $result[0];
        $line->setPositiveVoteCount(intval($result['positive_votes']))
            ->setNegativeVoteCount(intval($result['negative_votes']));

        return $line;
    }

    /**
     * Get a collection of lines within a file
     *
     * @param File $file
     * @return array An array of lines
     */
    public function getLinesByFile(File $file)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->addSelect([
                'SUM(CASE WHEN v.positive = true THEN 1 ELSE 0 END) AS positive_votes',
                'SUM(CASE WHEN v.positive = false THEN 1 ELSE 0 END) AS negative_votes',
            ])
            ->leftJoin('l.votes', 'v')
            ->where($qb->expr()->eq('l.file', ':file'))
            ->groupBy('l.id')
            ->setParameter('file', $file);

        $result = $qb->getQuery()->getResult();
        $lines = [];

        foreach ($result as $row) {
            $line = $row[0];
            $line->setPositiveVoteCount(intval($row['positive_votes']))
                ->setNegativeVoteCount(intval($row['negative_votes']));

            $lines[] = $line;
        }

        return $lines;
    }

    /**
     * Get the total number of lines
     *
     * Used on: homepage
     *
     * @return integer
     */
    public function getTotal()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('COUNT(l)')
            ->from('Entity:Line', 'l');

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * Get the next line that is able to be tweeted
     *
     * @return Line
     */
    public function getNextTweetableLine()
    {
        $queue = $this->getQueue(1);
        if (count($queue) === 0) {
            return $this->getRandom(1, true)[0];
        }

        return $queue[0];
    }

    /**
     * Get random line(s) from the database
     *
     * @param integer $count Number of lines to return
     * @param boolean $tweetable If true, all lines will be <= 140 characters
     *                           and won't have been tweeted
     * @return array An array of random lines
     */
    public function getRandom($count = 1, $tweetable = false)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('ChaosTangent\FansubEbooks\Entity\Line', 'l');

        /*
         * 1. Generate $count + buffer (min 10) of random numbers between 1 and
         *    the max ID sequence value of lines_id_seq + a 10% buffer
         * 3. Optionally filter those by ones that are tweetable
         * 4. Return $count of them
         */
        // based on http://stackoverflow.com/a/8675160/4545769
        $sql = 'SELECT '.$rsm->generateSelectClause().'
            FROM (
                SELECT DISTINCT 1 + trunc(random() * (nextval(:seq) * 1.1))::integer AS id
                FROM generate_series(1, :buffered_count) g
            ) r
            JOIN lines l USING (id)';

        if ($tweetable) {
            $sql .= ' WHERE l.charactercount <= 140
                AND l.id NOT IN (SELECT line_id FROM tweets)';
        }

        $sql .= ' LIMIT :count';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters([
            'seq' => 'lines_id_seq',
            'buffered_count' => ($count < 10) ? 10 : ceil($count * 1.5),
            'count' => $count,
        ]);

        /*
         * REALLY hacky, because $tweetable = true filters IDs AFTER they've
         * been randomly picked, the probably of not getting a result increases
         * the more lines have been tweeted so just do the query up to ten
         * times to make sure
         */
        if ($tweetable) {
            for ($i = 0; $i < 10; $i++) {
                $result = $query->getResult();
                if (count($result) > 0) {
                    return $result;
                }
            }
        }

        return $query->getResult();
    }

    /**
     * Get the current queue of tweetable lines
     *
     * @return array An array of tweetable lines
     */
    public function getQueue($count = 10)
    {
        $sqb = $this->_em->createQueryBuilder();
        $sqb->select('IDENTITY(t.line)')
            ->from('Entity:Tweet', 't');

        $qb = $this->createQueryBuilder('l');
        $qb->join('l.votes', 'v')
            ->where($qb->expr()->notIn('l.id', $sqb->getDql()))
            ->andWhere($qb->expr()->lte('l.characterCount', ':cc'))
            ->groupBy('l.id')
            ->having($qb->expr()->gte('SUM(CASE WHEN v.positive = true THEN 1 ELSE -1 END)', ':score'))
            ->setMaxResults($count)
            ->setParameters([
                'score' => 0,
                'cc' => 140,
            ]);

        return $qb->getQuery()->getResult();
    }

    /**
     * Search for a line
     *
     * @param string $q The search query
     * @param integer $page The page of results to retrieve
     * @param integer $perPage How many results to retrieve
     * @return SearchResult The search results
     * @see ChaosTangent\FansubEbooks\Bundle\AppBundle\DataFixtures\ORM\CreateSearchIndex
     */
    public function search($q, $page = 1, $perPage = 30, Series $series = null)
    {
        // default query parameters
        $defaultParams = [
            ':query' => $q,
            ':config' => 'english', // see CreateSearchIndex for this indexed value
        ];

        // default where clause
        $whereClause = 'WHERE to_tsvector(:config, l.line) @@ to_tsquery(:query)';
        if ($series !== null) {
            $whereClause .= ' AND f.series_id = :series';
            $defaultParams['series'] = $series->getId();
        }

        // count total results from search query
        $countSql = 'SELECT COUNT(l.id)
            FROM lines l
            JOIN files f ON f.id = l.file_id '.$whereClause;

        $total = $this->_em->getConnection()->fetchColumn($countSql, $defaultParams, 0);

        // get the selected page of results from search query
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('ChaosTangent\FansubEbooks\Entity\Line', 'l');

        $sql = 'SELECT '.$rsm->generateSelectClause().'
            FROM lines l
            JOIN files f ON f.id = l.file_id '.$whereClause.'
            ORDER BY ts_rank(to_tsvector(:config, l.line), to_tsquery(:query))
            LIMIT :limit OFFSET :offset';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters(array_merge($defaultParams, [
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage,
        ]));

        // bundle it all into a searchresult object
        return new SearchResult($q, $query->getResult(), $total, $page, $perPage);
    }
}
