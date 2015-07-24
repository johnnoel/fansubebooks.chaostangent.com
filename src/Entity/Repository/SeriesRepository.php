<?php

namespace ChaosTangent\FansubEbooks\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

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
     * @param integer $limit Maximum number of results to return
     * @param integer $offset Result offset
     * @return array An array of series that match the query
     * @see ChaosTangent\FansubEbooks\Bundle\AppBundle\DataFixtures\ORM\CreateSearchIndex
     */
    public function search($q, $limit = 30, $offset = 0)
    {
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
            'limit' => $limit,
            'offset' => $offset,
            'config' => 'english', // see CreateSearchIndex for this indexed value
        ]);

        return $query->getResult();
    }
}
