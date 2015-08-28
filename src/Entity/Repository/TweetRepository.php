<?php

namespace ChaosTangent\FansubEbooks\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\NoResultException;

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
     * Used on: homepage
     *
     * @return Tweet
     */
    public function getLatestTweets($count = 5)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->addSelect('l', 'f', 's')
            ->join('t.line', 'l')
            ->join('l.file', 'f')
            ->join('f.series', 's')
            ->orderBy('t.tweeted', 'DESC')
            ->setMaxResults($count);

        return $qb->getQuery()->getResult();
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
