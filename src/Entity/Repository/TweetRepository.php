<?php

namespace ChaosTangent\FansubEbooks\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\NoResultException;
use ChaosTangent\FansubEbooks\Entity\Result\PaginatedResult;

/**
 * Tweet entity repository
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class TweetRepository extends EntityRepository
{
    /**
     * Get the latest tweet with pre-populated line
     *
     * Used on: homepage, tweets
     *
     * @param integer $page
     * @param integer $perPage
     * @return Tweet
     */
    public function getLatest($page = 1, $perPage = 30)
    {
        $total = $this->getTotal();

        $qb = $this->createQueryBuilder('t');
        $qb->addSelect('l', 'f', 's')
            ->join('t.line', 'l')
            ->join('l.file', 'f')
            ->join('f.series', 's')
            ->orderBy('t.tweeted', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return new PaginatedResult($qb->getQuery()->getResult(), $total, $page, $perPage);
    }

    /**
     * Get the total number of tweets sent out
     *
     * Used on: homepage
     *
     * @return integer
     */
    public function getTotal()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('COUNT(t)')
            ->from('Entity:Tweet', 't');

        $query = $qb->getQuery();
        $query->useResultCache(true, 300, 'tweets/total');

        try {
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }
}
