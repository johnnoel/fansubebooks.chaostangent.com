<?php

namespace ChaosTangent\FansubEbooks\Entity\Repository;

use Doctrine\ORM\EntityRepository;

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
}
