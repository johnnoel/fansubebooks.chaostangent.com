<?php

namespace ChaosTangent\FansubEbooks\Entity\Quiz;

use Doctrine\ORM\Mapping as ORM;

/**
 * Quiz answer entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @ORM\Entity(repositoryClass="ChaosTangent\FansubEbooks\Entity\Repository\Quiz\AnswerRepository")
 * @ORM\Table(name="quiz_answers")
 */
class Answer
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\Quiz\Question", inversedBy="answers")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $question;
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\Quiz\User", inversedBy="answers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;
    /**
     * @ORM\Column(type="datetime")
     */
    private $asked;
    /**
     * @ORM\ManyToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\Series")
     * @ORM\JoinColumn(name="answer", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $answer;
    /**
     * @ORM\Column(type="boolean")
     */
    private $skipped = false;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $answered;

    /**
     * Set question
     *
     * @param Question $question
     * @return Answer
     */
    public function setQuestion(ChaosTangent\FansubEbooks\Entity\Quiz\Question $question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Answer
     */
    public function setUser(ChaosTangent\FansubEbook\Entity\Quiz\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set asked
     *
     * @param \DateTime $asked
     * @return Answer
     */
    public function setAsked(\DateTime $asked)
    {
        $this->asked = $asked;

        return $this;
    }

    /**
     * Get asked
     *
     * @return \DateTime
     */
    public function getAsked()
    {
        return $this->asked;
    }

    /**
     * Set answer
     *
     * @param Series $answer
     * @return Answer
     */
    public function setAnswer(ChaosTangent\FansubEbooks\Entity\Series $answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return Series
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set skipped
     *
     * @param boolean $skipped
     * @return Answer
     */
    public function setSkipped($skipped)
    {
        $this->skipped = $skipped;

        return $this;
    }

    /**
     * Get skipped
     *
     * @return boolean
     */
    public function getSkipped()
    {
        return $this->skipped;
    }

    /**
     * Set answered
     *
     * @param \DateTime $answered
     * @return Answer
     */
    public function setAnswered(\DateTime $answered)
    {
        $this->answered = $answered;

        return $this;
    }

    /**
     * Get answered
     *
     * @return \DateTime
     */
    public function getAnswered()
    {
        return $this->answered;
    }
}
