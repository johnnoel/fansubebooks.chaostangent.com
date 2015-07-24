<?php

namespace ChaosTangent\FansubEbooks\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use ChaosTangent\FansubEbooks\Entity\Result\SearchResult;
use ChaosTangent\FansubEbooks\Entity\Series;

/**
 * Line entity repository
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class LineRepository extends EntityRepository
{
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
        // count total results
        $countSql = 'SELECT COUNT(l.id)
            FROM lines l
            WHERE to_tsvector(:config, l.line) @@ to_tsquery(:query)';

        $total = $this->_em->getConnection()->fetchColumn($countSql, [
            ':config' => 'english',
            ':query' => $q,
        ], 0);

        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('ChaosTangent\FansubEbooks\Entity\Line', 'l');

        // todo decide on database platform
        $sql = 'SELECT '.$rsm->generateSelectClause().'
            FROM lines l
            WHERE to_tsvector(:config, l.line) @@ to_tsquery(:query)
            ORDER BY ts_rank(to_tsvector(:config, l.line), to_tsquery(:query))
            LIMIT :limit OFFSET :offset';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters([
            'query' => $q,
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage,
            'config' => 'english', // see CreateSearchIndex for this indexed value
        ]);

        return new SearchResult($q, $query->getResult(), $total, $page, $perPage);
    }
}
