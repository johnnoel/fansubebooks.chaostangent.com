<?php

namespace ChaosTangent\FansubEbooks\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Suggestion entity repository
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SuggestionRepository extends EntityRepository
{
    /**
     * Get a count of the flags according to a set of criteria
     *
     * @param array $criteria Can be ip, start, end or line
     * @return integer
     */
    public function getSuggestionCount(array $criteria = [])
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select([ 'COUNT(s)' ])
            ->from('Entity:Suggestion', 's');

        foreach ($criteria as $key => $value) {
            switch ($key) {
            case 'ip':
                $qb->andWhere($qb->expr()->eq('s.ip', ':ip'))
                    ->setParameter('ip', $value);
                break;
            case 'start':
                $qb->andWhere($qb->expr()->gte('s.added', ':start'))
                    ->setParameter('start', $value);
                break;
            case 'end':
                $qb->andWhere($qb->expr()->lte('s.added', ':end'))
                    ->setParameter('end', $value);
                break;
            case 'type':
                $qb->andWhere($qb->expr()->eq('s.type', ':type'))
                    ->setParameter('type', $value);
                break;
            }
        }

        return intval($qb->getQuery()->getSingleScalarResult());

    }

    /**
     * Get suggestions that occurred within a specific timeframe
     *
     * @param \DateTime $start
     * @param \DateTime $finish
     * @return array An array of suggestions
     */
    public function getByAdded(\DateTime $start = null, \DateTime $finish = null)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->orderBy('s.added', 'DESC');

        if ($start !== null) {
            $qb->andWhere($qb->expr()->gte('s.added', ':start'))
                ->setParameter('start', $start);
        }

        if ($finish !== null) {
            $qb->andWhere($qb->expr()->lte('s.added', ':finish'))
                ->setParameter('finish', $finish);
        }

        return $qb->getQuery()->getResult();
    }
}
