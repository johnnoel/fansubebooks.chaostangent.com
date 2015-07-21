<?php

namespace ChaosTangent\FansubEbooks\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * Line entity repository
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class LineRepository extends EntityRepository
{
    /**
     * Get the total number of lines
     *
     * Used on: homepage
     *
     * @return integer
     */
    public function getTotal()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('COUNT(l)')
            ->from('Entity:Line', 'l');

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * Get the next line that is able to be tweeted
     *
     * @return Line
     */
    public function getNextTweetableLine()
    {
        $queue = $this->getQueue(1);
        if (count($queue) === 0) {
            return $this->getRandom(1, true);
        }

        return reset($queue);
    }

    /**
     * Get random line(s) from the database
     *
     * @param integer $count Number of lines to return
     * @param boolean $tweetable If true, all lines will be <= 140 characters
     *                           and won't have been tweeted
     * @return array An array of random lines
     */
    public function getRandom($count = 1, $tweetable = false)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('ChaosTangent\FansubEbooks\Entity\Line', 'l');
        $rsm->addJoinedEntityFromClassMetadata('ChaosTangent\FansubEbooks\Entity\File', 'f', 'l', 'file', [
            'id' => 'file_id',
        ]);

        $sql = 'SELECT '.$rsm->generateSelectClause().' FROM lines l
            JOIN files f ON l.file_id = f.id';

        if ($tweetable) {
            $sql .= ' WHERE l.charactercount <= 140
                AND l.id NOT IN (SELECT line_id FROM tweets)';
        }

        $sql .= ' OFFSET random() * :offset LIMIT :limit';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters([
            'offset' => $this->getTotal(),
            'limit' => $count,
        ]);

        return $query->getResult();
    }

    /**
     * Get the current queue of tweetable lines
     *
     * @return array An array of tweetable lines
     */
    public function getQueue($count = 10)
    {
        $sqb = $this->_em->createQueryBuilder();
        $sqb->select('IDENTITY(t.line)')
            ->from('Entity:Tweet', 't');

        $qb = $this->createQueryBuilder('l');
        $qb->addWhere('SUM(CASE l.positive THEN 1 ELSE -1 END) AS score')
            ->join('l.votes', 'v')
            ->where($qb->expr()->notIn('l.id', $sqb->getDql()))
            ->andWhere($qb->expr()->lte('l.characterCount', ':cc'))
            ->groupBy('l.id')
            ->having($qb->expr()->gte('score', ':score'))
            ->setMaxResults($count)
            ->setParameters([
                'score' => 0,
                'cc' => 140,
            ]);

        return $qb->getQuery()->getResult();
    }
}
