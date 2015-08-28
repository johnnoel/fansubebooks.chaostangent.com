<?php

namespace ChaosTangent\FansubEbooks\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Flag entity repository
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class FlagRepository extends EntityRepository
{
    /**
     * Get a count of the flags according to a set of criteria
     *
     * @param array $criteria Can be ip, start, end or line
     * @return integer
     */
    public function getFlagCount(array $criteria = [])
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select([ 'COUNT(f)' ])
            ->from('Entity:Flag', 'f');

        foreach ($criteria as $key => $value) {
            switch ($key) {
            case 'ip':
                $qb->andWhere($qb->expr()->eq('f.ip', ':ip'))
                    ->setParameter('ip', $value);
                break;
            case 'start':
                $qb->andWhere($qb->expr()->gte('f.added', ':start'))
                    ->setParameter('start', $value);
                break;
            case 'end':
                $qb->andWhere($qb->expr()->lte('f.added', ':end'))
                    ->setParameter('end', $value);
                break;
            case 'line':
                $qb->andWhere($qb->expr()->eq('f.line', ':line'))
                    ->setParameter('line', $value);
                break;
            }
        }

        return intval($qb->getQuery()->getSingleScalarResult());

    }

    /**
     * Get flags and associated lines within a time period
     *
     * @param \DateTime $start
     * @param \DateTime $end
     * @return array
     */
    public function getByAdded(\DateTime $start = null, \DateTime $end = null)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->addSelect('l')
            ->join('f.line', 'l')
            ->orderBy('f.added', 'DESC');

        if ($start !== null) {
            $qb->andWhere($qb->expr()->gte('f.added', ':start'))
                ->setParameter('start', $start);
        }

        if ($end !== null) {
            $qb->andWhere($qb->expr()->lte('f.added', ':end'))
                ->setParameter('end', $end);
        }

        return $qb->getQuery()->getResult();
    }
}
