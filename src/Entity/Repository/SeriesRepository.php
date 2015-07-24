<?php

namespace ChaosTangent\FansubEbooks\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use ChaosTangent\FansubEbooks\Entity\Result\SearchResult;

/**
 * Series entity repository
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SeriesRepository extends EntityRepository
{
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

        // fetch page of results
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('ChaosTangent\FansubEbooks\Entity\Series', 's');

        $sql = 'SELECT '.$rsm->generateSelectClause().'
            FROM series s
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

        return new SearchResult($q, $query->getResult(), $total, $page, $perPage);
    }
}
