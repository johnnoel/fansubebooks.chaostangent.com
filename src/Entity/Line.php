<?php

namespace ChaosTangent\FansubEbooks\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Line entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @ORM\Entity(repositoryClass="ChaosTangent\FansubEbooks\Entity\Repository\LineRepository")
 * @ORM\Table(name="lines")
 */
class Line
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\ReadOnly
     * @Serializer\Type("integer")
     */
    private $id;
    /**
     * @ORM\Column(type="text")
     * @Serializer\Type("string")
     */
    private $line;
    /**
     * @ORM\Column(name="charactercount",type="integer")
     * @Serializer\Type("integer")
     */
    private $characterCount;
    /**
     * @ORM\ManyToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\File", inversedBy="lines")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @Serializer\Exclude
     */
    private $file;
    /**
     * @ORM\OneToMany(targetEntity="ChaosTangent\FansubEbooks\Entity\Vote", mappedBy="line")
     * @Serializer\Exclude
     */
    private $votes;
    /**
     * @ORM\OneToMany(targetEntity="ChaosTangent\FansubEbooks\Entity\Flag", mappedBy="line")
     * @Serializer\Exclude
     */
    private $flags;
    /**
     * @ORM\OneToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\Tweet", mappedBy="line")
     * @Serializer\Exclude
     */
    private $tweet;
    /**
     * @var integer
     * @Serializer\Type("integer")
     */
    private $positiveVoteCount = 0;
    /**
     * @var integer
     * @Serializer\Type("integer")
     */
    private $negativeVoteCount = 0;
    /**
     * @var string
     * @Serializer\Type("string")
     */
    private $tweetId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->votes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param string $line
     * @return Line
     */
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line
     *
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Set characterCount
     *
     * @param integer $characterCount
     * @return Line
     */
    public function setCharacterCount($characterCount)
    {
        $this->characterCount = $characterCount;

        return $this;
    }

    /**
     * Get characterCount
     *
     * @return integer
     */
    public function getCharacterCount()
    {
        return $this->characterCount;
    }

    /**
     * Set positiveVoteCount
     *
     * @param integer $positiveVoteCount
     * @return Line
     */
    public function setPositiveVoteCount($positiveVoteCount)
    {
        $this->positiveVoteCount = $positiveVoteCount;

        return $this;
    }

    /**
     * Get positiveVoteCount
     *
     * @return integer
     */
    public function getPositiveVoteCount()
    {
        return $this->positiveVoteCount;
    }

    /**
     * Set negativeVoteCount
     *
     * @param integer $negativeVoteCount
     * @return Line
     */
    public function setNegativeVoteCount($negativeVoteCount)
    {
        $this->negativeVoteCount = $negativeVoteCount;

        return $this;
    }

    /**
     * Get negativeVoteCount
     *
     * @return integer
     */
    public function getNegativeVoteCount()
    {
        return $this->negativeVoteCount;
    }

    /**
     * Set tweetId
     *
     * @param string $tweetId
     * @return Line
     */
    public function setTweetId($tweetId)
    {
        $this->tweetId = $tweetId;

        return $this;
    }

    /**
     * Get tweetId
     *
     * @return string
     */
    public function getTweetId()
    {
        return $this->tweetId;
    }

    /**
     * Whether this line has been tweeted
     *
     * @return boolean
     */
    public function hasBeenTweeted()
    {
        return $this->tweetId !== null;
    }

    /**
     * Set file
     *
     * @param \ChaosTangent\FansubEbooks\Entity\File $file
     * @return Line
     */
    public function setFile(\ChaosTangent\FansubEbooks\Entity\File $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return \ChaosTangent\FansubEbooks\Entity\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Add votes
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Vote $votes
     * @return Line
     */
    public function addVote(\ChaosTangent\FansubEbooks\Entity\Vote $votes)
    {
        $this->votes[] = $votes;

        return $this;
    }

    /**
     * Remove votes
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Vote $votes
     */
    public function removeVote(\ChaosTangent\FansubEbooks\Entity\Vote $votes)
    {
        $this->votes->removeElement($votes);
    }

    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Add flags
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Flag $flags
     * @return Line
     */
    public function addFlag(\ChaosTangent\FansubEbooks\Entity\Flag $flags)
    {
        $this->flags[] = $flags;

        return $this;
    }

    /**
     * Remove flags
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Flag $flags
     */
    public function removeFlag(\ChaosTangent\FansubEbooks\Entity\Flag $flags)
    {
        $this->flags->removeElement($flags);
    }

    /**
     * Get flags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Set tweet
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Tweet $tweet
     * @return Line
     */
    public function setTweet(\ChaosTangent\FansubEbooks\Entity\Tweet $tweet)
    {
        $this->tweet = $tweet;

        return $this;
    }

    /**
     * Get tweet
     *
     * @return \ChaosTangent\FansubEbooks\Entity\Tweet
     */
    public function getTweet()
    {
        return $this->tweet;
    }

    /**
     * Get the series for this line
     *
     * Shortcut for $line->getFile()->getSeries()
     *
     * @return Series
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("series")
     * @Serializer\Type("ChaosTangent\FansubEbooks\Entity\Series")
     */
    public function getSeries()
    {
        return $this->file->getSeries();
    }
}
