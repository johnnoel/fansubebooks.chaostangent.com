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
    public function getLatestTweet()
    {
        $qb = $this->createQueryBuilder('t');
        $qb->addSelect('l')
            ->join('t.line', 'l')
            ->orderBy('t.tweeted', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
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

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }
}