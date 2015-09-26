<?php

namespace Entity\Repository\Quiz;

use Doctrine\ORM\EntityRepository;
use ChaosTangent\FansubEbook\Entity\Quiz\User,
    ChaosTangent\FansubEbook\Entity\Quiz\Answer,
    ChaosTangent\FansubEbook\Entity\Quiz\Question;

/**
 * Quiz question repository
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class QuestionRepository extends EntityRepository
{
    /**
     * What is the minimum number of characters in a line before it can be
     * considered suitable for a question?
     *
     * @var integer
     */
    const MIN_LINE_LENGTH = 70;
    /**
     * What is the probability of a new question being asked every time
     * getNextQuestion is called? This is subtracted from one and compared with
     * an mt_rand() call
     *
     * @var float
     */
    const NEW_QUESTION_PROBABILITY = 0.5;

    /** @var AnswerRepository */
    protected $answerRepo;

    public function setAnswerRepository(AnswerRepository $answerRepo)
    {
        $this->answerRepo = $answerRepo;
        return $this;
    }

    /**
     * Get the next question for the particular user
     *
     * This function will return the same as getCurrentQuestion if the User has
     * not answered their question
     *
     * @param User $user
     * @return Question
     */
    public function getNextQuestion(User $user)
    {
        // do we have an unanswered question already?
        $current = $this->getCurrentQuestion($user);
        if ($current !== null) {
            return $current;
        }

        // choose whether to ask an existing question or generate a new one
        if (mt_rand() >= self::NEW_QUESTION_PROBABILITY) {
            $question = $this->getNewQuestion();
            if ($question === null) {
                $question = $this->getExistingQuestion($user);

                if ($question === null) {
                    // oh god, they've answered everything...
                }
            }
        } else {
            $question = $this->getExistingQuestion($user);
            if ($question === null) {
                $question = $this->getNewQuestion();

                if ($question === null) {
                    // run away!
                }
            }
        }

        return $question;
    }

    /**
     * Get the current question for the particular user
     *
     * Will return null if the User does not have a question pending
     *
     * @param User $user
     * @return Question|null
     */
    public function getCurrentQuestion(User $user)
    {
        $qb = $this->createQueryBuilder('q');
        $qb->join('q.answers', 'a')
            ->where($qb->expr()->eq('a.user', ':user'))
            ->andWhere($qb->expr()->isNull('a.answered'))
            ->setParameter('user', $user)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Get a question that has already been asked, just not for this user
     *
     * Will return null if there are no existing questions that the user hasn't
     * answered (or if there are no existing questions)
     *
     * @param User $user
     * @return Question|null
     */
    protected function getExistingQuestion(User $user)
    {
        $sql = 'WITH asked (SELECT a.question_id FROM quiz_answers a WHERE a.user_id != :user)
            SELECT * FROM quiz_questions q
                WHERE q.id NOT IN (asked)
                LIMIT 1';

        $query = $this->_em->createNativeQuery($sql);
        $query->setParameters();

        return $query->getOneOrNullResult();
    }

    /**
     * Generate a new question
     *
     * Will return null if there are no more lines that are suitable for
     * questions (i.e. > self::MIN_LINE_LENGTH)
     *
     * @return Question|null
     */
    protected function getNewQuestion()
    {
        $sql = 'WITH asked (SELECT line_id FROM quiz_questions)
            SELECT * FROM lines l
                WHERE l.charactercount > :cc
                    AND l.id IS NOT IN (asked)
                LIMIT 1';

        $query = $this->_em->createNativeQuery($sql);
        $query->setParameters();

        return $query->getOneOrNullResult();
    }
}
