<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use ChaosTangent\FansubEbooks\Entity\Line,
    ChaosTangent\FansubEbooks\Entity\File,
    ChaosTangent\FansubEbooks\Entity\Series;
use ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\User,
    ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Answer,
    ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Question;

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

        $question = null;

        // choose whether to ask an existing question or generate a new one
        if ((mt_rand() / mt_getrandmax()) < self::NEW_QUESTION_PROBABILITY) {
            $question = $this->getExistingQuestion($user);
        }

        if ($question === null) {
            $question = $this->getNewQuestion();
        }

        if ($question === null) {
            // oh god, they've answered everything...
            throw new \Exception('ARARARARARARARAR');
        }

        // now bind this question to this user
        $answer = new Answer();
        $answer->setQuestion($question)
            ->setUser($user);

        $this->_em->persist($answer);
        $this->_em->flush();

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
        // count
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addScalarResult('question_count', 'question_count', 'integer');

        $countSql = 'SELECT COUNT(*) AS question_count
            FROM quiz_questions q
            WHERE q.id NOT IN (SELECT question_id FROM quiz_answers WHERE user_id = :user)';
        $countQuery = $this->_em->createNativeQuery($countSql, $rsm);
        $countQuery->setParameter('user', $user->getId());

        $count = $countQuery->getSingleScalarResult();

        // question
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata(Question::class, 'q');

        $questionSql = 'SELECT '.$rsm->generateSelectClause([ 'q' => 'q' ]).'
            FROM quiz_questions q
            WHERE q.id NOT IN (SELECT question_id FROM quiz_answers WHERE user_id = :user)
            OFFSET random() * :qc LIMIT 1';

        $questionQuery = $this->_em->createNativeQuery($questionSql, $rsm);
        $questionQuery->setParameters([
            'user' => $user->getId(),
            'qc' => $count,
        ]);

        return $questionQuery->getOneOrNullResult();
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
        // count
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addScalarResult('line_count', 'line_count', 'integer');

        $countSql = 'SELECT COUNT(*) AS line_count
            FROM lines l
            WHERE l.charactercount > :cc
                AND l.id NOT IN (SELECT line_id FROM quiz_questions)';

        $countQuery = $this->_em->createNativeQuery($countSql, $rsm);
        $countQuery->setParameter('cc', self::MIN_LINE_LENGTH);

        // todo query cache this

        $count = $countQuery->getSingleScalarResult();

        // line
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata(Line::class, 'l');
        $rsm->addJoinedEntityFromClassMetadata(File::class, 'f', 'l', 'file', [ 'id' => 'file_id' ]);
        $rsm->addJoinedEntityFromClassMetadata(Series::class, 's', 'f', 'series', [ 'id' => 'series_id', 'added' => 'series_added' ]);

        $lineSql = 'SELECT '.$rsm->generateSelectClause([ 'l' => 'l' ]).'
            FROM lines l
            INNER JOIN files f ON f.id = l.file_id
            INNER JOIN series s ON s.id = f.series_id
            WHERE l.charactercount > :cc
            OFFSET random() * :lc LIMIT 1';
        $lineQuery = $this->_em->createNativeQuery($lineSql, $rsm);
        $lineQuery->setParameters([
            'cc' => self::MIN_LINE_LENGTH,
            'lc' => $count,
        ]);

        $line = $lineQuery->getOneOrNullResult();

        if ($line === null) {
            throw new \Exception('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAieeeeee');
        }

        // create our question
        $question = new Question();
        $question->setLine($line);

        // pick a random spot for the correct answer
        $correctPos = mt_rand(1, 3);
        for ($i = 1; $i <= 3; $i++) {
            $method = 'setSeries'.$i;

            if ($i == $correctPos) {
                $question->$method($line->getSeries());
            } else {
                // get a random series for the other spots
                $question->$method($this->getRandomSeries($line->getSeries()));
            }
        }

        $this->_em->persist($question);
        $this->_em->flush();

        return $question;
    }

    /**
     * Get a random series that is not $not
     *
     * @param Series $not
     * @return Series
     */
    protected function getRandomSeries(Series $not)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('COUNT(s)')
            ->from(Series::class, 's');

        $count = $qb->getQuery()->getSingleScalarResult();

        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata(Series::class, 's');

        $sql = 'SELECT '.$rsm->generateSelectClause([ 's' => 's' ]).'
            FROM series s
            WHERE s.id != :sid
            OFFSET random() * :sc LIMIT 1';
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters([
            'sid' => $not->getId(),
            'sc' => intval($count) - 1,
        ]);

        return $query->getOneOrNullResult();
    }
}
