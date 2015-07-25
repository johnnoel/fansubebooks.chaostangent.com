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
     * Get the number of votes with an IP address for a given period
     *
     * @param string $ip The ip address to search for
     * @param \DateTime $start Start date/time
     * @param \DateTime $end End date/time, leave off for all votes after start
     * @return integer
     */
    public function getIpAddressCountForPeriod($ip, \DateTime $start, \DateTime $end = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select([ 'COUNT(v)' ])
            ->from('Entity:Vote', 'v')
            ->where($qb->expr()->eq('v.ip', ':ip'))
            ->andWhere($qb->expr()->gte('v.added', ':start'))
            ->setParameters([
                'ip' => $ip,
                'start' => $start,
            ]);

        if ($end !== null) {
            $qb->andWhere($qb->expr()->lte('v.added', ':end'))
                ->setParameter('end', $end);
        }

        return intval($qb->getQuery()->getSingleScalarResult());
    }
}
