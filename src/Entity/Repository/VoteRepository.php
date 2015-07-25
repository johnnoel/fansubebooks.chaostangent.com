<?php

namespace ChaosTangent\FansubEbooks\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Vote entity repository
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class VoteRepository extends EntityRepository
{
    /**
     * Get a count of the votes according to a set of criteria
     *
     * @param array $criteria Can be ip, start, end or line
     * @return integer
     */
    public function getVoteCount(array $criteria = [])
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select([ 'COUNT(v)' ])
            ->from('Entity:Vote', 'v');

        foreach ($criteria as $key => $value) {
            switch ($key) {
            case 'ip':
                $qb->andWhere($qb->expr()->eq('v.ip', ':ip'))
                    ->setParameter('ip', $value);
                break;
            case 'start':
                $qb->andWhere($qb->expr()->gte('v.added', ':start'))
                    ->setParameter('start', $value);
                break;
            case 'end':
                $qb->andWhere($qb->expr()->lte('v.added', ':end'))
                    ->setParameter('end', $value);
                break;
            case 'line':
                $qb->andWhere($qb->expr()->eq('v.line', ':line'))
                    ->setParameter('line', $value);
                break;
            }
        }

        return intval($qb->getQuery()->getSingleScalarResult());
    }
}
