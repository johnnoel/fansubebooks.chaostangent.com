<?php

namespace ChaosTangent\FansubEbooks\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use ChaosTangent\FansubEbooks\Entity\Result\PaginatedResult,
    ChaosTangent\FansubEbooks\Entity\Result\SearchResult;

/**
 * Series entity repository
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SeriesRepository extends EntityRepository
{
    public function getSeries($alias)
    {
        $fileSql = 'SELECT COUNT(f.id) AS file_count FROM series s
            JOIN files f ON f.series_id = s.id
            WHERE s.alias = :alias
            GROUP BY s.id';

        $lineSql = 'SELECT COUNT(l.id) AS line_count FROM series s
            JOIN files f ON f.series_id = s.id
            JOIN lines l ON l.file_id = f.id
            WHERE s.alias = :alias
            GROUP BY s.id';

        $tweetSql = 'SELECT COUNT(t.id) AS tweet_count FROM series s
            JOIN files f ON f.series_id = s.id
            JOIN lines l ON l.file_id = f.id
            JOIN tweets t ON t.line_id = l.id
            WHERE s.alias = :alias
            GROUP BY s.id';

        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('ChaosTangent\FansubEbooks\Entity\Series', 's');
        $rsm->addScalarResult('file_count', 'file_count');
        $rsm->addScalarResult('line_count', 'line_count');
        $rsm->addScalarResult('tweet_count', 'tweet_count');

        $sql = 'WITH file_count AS ('.$fileSql.'), line_count AS ('.$lineSql.'), tweet_count AS ('.$tweetSql.')
            SELECT '.$rsm->generateSelectClause().', file_count.file_count, line_count.line_count, tweet_count.tweet_count
                FROM series s, file_count, line_count, tweet_count
                WHERE s.alias = :alias
                LIMIT 1';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('alias', $alias);
        $result = $query->getOneOrNullResult();

        return $result[0]->setFileCount(intval($result['file_count']))
            ->setLineCount(intval($result['line_count']))
            ->setTweetCount(intval($result['tweet_count']));
    }

    /**
     * Get a paginated list of series
     *
     * @param integer $page
     * @param integer $perPage
     * @return PaginatedResult
     */
    public function getAllSeries($page = 1, $perPage = 30)
    {
        // get total number of series
        $qb = $this->_em->createQueryBuilder();
        $qb->select([ 'COUNT(s.id)' ])
            ->from('Entity:Series', 's');

        $total = $qb->getQuery()->getSingleScalarResult();

        // get series
        $fileSql = 'SELECT fs.id, COUNT(f.id) AS file_count
            FROM series fs
            JOIN files f ON f.series_id = fs.id
            GROUP BY fs.id';

        $lineSql = 'SELECT ls.id, COUNT(l.id) AS line_count
            FROM series ls
            JOIN files lf ON lf.series_id = ls.id
            JOIN lines l ON l.file_id = lf.id
            GROUP BY ls.id';

        $tweetSql = 'SELECT ts.id, COUNT(t.id) AS tweet_count
            FROM series ts
            JOIN files tf ON tf.series_id = ts.id
            JOIN lines tl ON tl.file_id = tf.id
            JOIN tweets t ON t.line_id = tl.id
            GROUP BY ts.id';


        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('ChaosTangent\FansubEbooks\Entity\Series', 's');
        $rsm->addScalarResult('file_count', 'file_count');
        $rsm->addScalarResult('line_count', 'line_count');
        $rsm->addScalarResult('tweet_count', 'tweet_count');

        $sql = 'WITH file_counts AS ('.$fileSql.'), line_counts AS ('.$lineSql.'), tweet_counts AS ('.$tweetSql.')
            SELECT '.$rsm->generateSelectClause().', fc.file_count, lc.line_count, tc.tweet_count
                FROM series s
                LEFT JOIN file_counts fc ON fc.id = s.id
                LEFT JOIN line_counts lc ON lc.id = s.id
                LEFT JOIN tweet_counts tc ON tc.id = s.id
                ORDER BY s.title ASC
                LIMIT :limit OFFSET :offset';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters([
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage,
        ]);

        $result = $query->getResult();
        $ret = [];

        foreach ($result as $row) {
            $ret[] = $row[0]
                ->setFileCount($row['file_count'])
                ->setLineCount($row['line_count'])
                ->setTweetCount($row['tweet_count']);
        }

        return new PaginatedResult($ret, $total, $page, $perPage);
    }

    public function getTotalSeries(array $criteria = [])
    {
    }

    /**
     * Search for a series
     *
     * @param string $q The search query
     * @param integer $page The page of results to retrieve
     * @param integer $perPage How many results to retrieve
     * @return SearchResult The search results
     * @see ChaosTangent\FansubEbooks\Bundle\AppBundle\DataFixtures\ORM\CreateSearchIndex
     */
    public function search($q, $page = 1, $perPage = 30)
    {
        // count total results
        $countSql = 'SELECT COUNT(s.id)
            FROM series s
            WHERE to_tsvector(:config, s.title) @@ to_tsquery(:query)';

        $total = $this->_em->getConnection()->fetchColumn($countSql, [
            ':config' => 'english',
            ':query' => $q,
        ], 0);

        $fileSql = 'SELECT fs.id, COUNT(f.id) AS file_count
            FROM series fs
            JOIN files f ON f.series_id = fs.id
            GROUP BY fs.id';

        $lineSql = 'SELECT ls.id, COUNT(l.id) AS line_count
            FROM series ls
            JOIN files lf ON lf.series_id = ls.id
            JOIN lines l ON l.file_id = lf.id
            GROUP BY ls.id';

        $tweetSql = 'SELECT ts.id, COUNT(t.id) AS tweet_count
            FROM series ts
            JOIN files tf ON tf.series_id = ts.id
            JOIN lines tl ON tl.file_id = tf.id
            JOIN tweets t ON t.line_id = tl.id
            GROUP BY ts.id';

        // fetch page of results
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('ChaosTangent\FansubEbooks\Entity\Series', 's');
        $rsm->addScalarResult('file_count', 'file_count', 'integer');
        $rsm->addScalarResult('line_count', 'line_count', 'integer');
        $rsm->addScalarResult('tweet_count', 'tweet_count', 'integer');

        $sql = 'WITH file_counts AS ('.$fileSql.'), line_counts AS ('.$lineSql.'), tweet_counts AS ('.$tweetSql.')
            SELECT '.$rsm->generateSelectClause().', fc.file_count, lc.line_count, tc.tweet_count
                FROM series s
                LEFT JOIN file_counts fc ON fc.id = s.id
                LEFT JOIN line_counts lc ON lc.id = s.id
                LEFT JOIN tweet_counts tc ON tc.id = s.id
                WHERE to_tsvector(:config, s.title) @@ to_tsquery(:query)
                ORDER BY ts_rank(to_tsvector(:config, s.title), to_tsquery(:query))
                LIMIT :limit OFFSET :offset';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters([
            'query' => $q,
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage,
            'config' => 'english', // see CreateSearchIndex for this indexed value
        ]);

        $result = $query->getResult();
        $ret = [];

        foreach ($result as $row) {
            $ret[] = $row[0]->setFileCount($row['file_count'])
                ->setLineCount($row['line_count'])
                ->setTweetCount($row['tweet_count']);
        }

        return new SearchResult($q, $ret, $total, $page, $perPage);
    }

    /**
     * Get the most recently added or updated series
     *
     * @param integer $count
     * @return array An array of recently updated series
     */
    public function getMostRecentlyUpdated($count = 10)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->addSelect([ 'GREATEST(MAX(f.added), s.added) AS last_updated' ])
            ->join('s.files', 'f')
            ->groupBy('s.id')
            ->orderBy('last_updated', 'DESC')
            ->setMaxResults($count);

        $result = $qb->getQuery()->getResult();
        $ret = [];

        foreach ($result as $row) {
            $ret[] = $row[0]->setUpdated($row['last_updated']);
        }

        return $ret;
    }
}
