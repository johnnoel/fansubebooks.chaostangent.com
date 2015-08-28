<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Activity;

use ChaosTangent\FansubEbooks\Entity\Line,
    ChaosTangent\FansubEbooks\Entity\Suggestion;

/**
 * Activity entry entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class Entry
{
    const ACTIVITY_VOTE_UP = 1;
    const ACTIVITY_VOTE_DOWN = 2;
    const ACTIVITY_FLAG = 3;
    const ACTIVITY_SUGGEST_SERIES = 4;
    const ACTIVITY_SUGGEST_SCRIPT = 5;
    const ACTIVITY_SERIES_ADD = 6;

    /**
     * What date and time this activity took place
     *
     * @var \DateTime
     */
    private $dateTime;
    /**
     * The line (if any) that was the endpoint of the activity
     *
     * @var Line
     */
    private $line;
    /**
     * The suggestion (if any) that was the endpoint of this activity
     *
     * @var Suggestion
     */
    private $suggestion;
    /**
     * The series (if any) that was the endpoint of this activity
     *
     * @var Series
     */
    private $series;
    /**
     * The type of activity
     *
     * @var integer
     */
    private $type;

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     * @return Entry
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set line
     *
     * @param Line $line
     * @return Entry
     */
    public function setLine($line)
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
     * Set suggestion
     *
     * @param Suggestion $suggestion
     * @return Entry
     */
    public function setSuggestion($suggestion)
    {
        $this->suggestion = $suggestion;

        return $this;
    }

    /**
     * Get suggestion
     *
     * @return Suggestion
     */
    public function getSuggestion()
    {
        return $this->suggestion;
    }

    /**
     * Set series
     *
     * @param Series $series
     * @return Entry
     */
    public function setSeries($series)
    {
        $this->series = $series;

        return $this;
    }

    /**
     * Get series
     *
     * @return Series
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Entry
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return a plain-text representation of the type
     *
     * Useful for CSS classes and the like
     *
     * @return string
     */
    public function getTypeAsText()
    {
        switch ($this->type) {
        case self::ACTIVITY_SERIES_ADD:
            return 'series';
        case self::ACTIVITY_VOTE_UP:
            return 'voteup';
        case self::ACTIVITY_VOTE_DOWN:
            return 'votedown';
        case self::ACTIVITY_FLAG:
            return 'flag';
        case self::ACTIVITY_SUGGEST_SERIES:
            return 'suggestseries';
        case self::ACTIVITY_SUGGEST_SCRIPT:
            return 'suggestscript';
        }

        return null;
    }

    /**
     * Get printable message for this activity
     *
     * @return string
     */
    public function getMessage()
    {
        if ($this->getType() == self::ACTIVITY_SERIES_ADD) {
            return 'New series "'.$this->series->getTitle().'" added';
        } else if ($this->getType() == self::ACTIVITY_VOTE_UP) {
            return 'Line voted up';
        } else if ($this->getType() == self::ACTIVITY_VOTE_DOWN) {
            return 'Line voted down';
        } else if ($this->getType() == self::ACTIVITY_SUGGEST_SERIES) {
            return 'New series suggested';
        } else if ($this->getType() == self::ACTIVITY_SUGGEST_SCRIPT) {
            return 'New script submitted';
        } else if ($this->getType() == self::ACTIVITY_FLAG) {
            return 'Line flagged as bad';
        }

        return 'Well, something happened';
    }
}
