<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Quiz question entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @ORM\Entity(repositoryClass="ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Repository\QuestionRepository")
 * @ORM\Table(name="quiz_questions")
 */
class Question
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\Line")
     * @ORM\JoinColumn(name="line_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $line;
    /**
     * @ORM\ManyToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\Series")
     * @ORM\JoinColumn(name="series_1_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $series1;
    /**
     * @ORM\ManyToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\Series")
     * @ORM\JoinColumn(name="series_2_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $series2;
    /**
     * @ORM\ManyToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\Series")
     * @ORM\JoinColumn(name="series_3_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $series3;
    /**
     * @ORM\OneToMany(targetEntity="ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Answer", mappedBy="question")
     */
    private $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set line
     *
     * @param Line $line
     * @return Question
     */
    public function setLine(\ChaosTangent\FansubEbooks\Entity\Line $line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line
     *
     * @return Line
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Set series1
     *
     * @param Series $series1
     * @return Question
     */
    public function setSeries1(\ChaosTangent\FansubEbooks\Entity\Series $series1)
    {
        $this->series1 = $series1;

        return $this;
    }

    /**
     * Get series1
     *
     * @return Series
     */
    public function getSeries1()
    {
        return $this->series1;
    }

    /**
     * Set series2
     *
     * @param Series $series2
     * @return Question
     */
    public function setSeries2(\ChaosTangent\FansubEbooks\Entity\Series $series2)
    {
        $this->series2 = $series2;

        return $this;
    }

    /**
     * Get series2
     *
     * @return Series
     */
    public function getSeries2()
    {
        return $this->series2;
    }

    /**
     * Set series3
     *
     * @param Series $series3
     * @return Question
     */
    public function setSeries3(\ChaosTangent\FansubEbooks\Entity\Series $series3)
    {
        $this->series3 = $series3;

        return $this;
    }

    /**
     * Get series3
     *
     * @return Series
     */
    public function getSeries3()
    {
        return $this->series3;
    }

    /**
     * Set answers
     *
     * @param array|ArrayCollection $answers
     * @return Question
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;

        return $this;
    }

    /**
     * Get answers
     *
     * @return array|ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Is the passed series ID a valid answer for this question?
     *
     * @param mixed $seriesId
     * @return boolean
     */
    public function isValidAnswer($seriesId)
    {
        $answers = [ $this->series1->getId(), $this->series2->getId(), $this->series3->getId() ];
        return in_array($seriesId, $answers);
    }
}
