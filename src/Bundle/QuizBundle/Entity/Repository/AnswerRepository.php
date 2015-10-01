<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\User,
    ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Answer,
    ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Question;

/**
 * Answer repository
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class AnswerRepository extends EntityRepository
{
    /**
     * Update a user's answer to a question
     *
     * @param Answer $answer
     */
    public function update(Answer $answer)
    {
        $this->_em->persist($answer);
        $this->_em->flush();
    }

    /**
     * Get the current answer for a user
     *
     * @param User $user
     * @return Answer|null
     */
    public function getCurrentAnswer(User $user)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->join('a.question', 'q')
            ->where($qb->expr()->eq('a.user', ':user'))
            ->andWhere($qb->expr()->isNull('a.answer'))
            ->andWhere($qb->expr()->eq('a.skipped', ':skipped'))
            ->setMaxResults(1)
            ->setParameters([
                'user' => $user,
                'skipped' => false,
            ]);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
