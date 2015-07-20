<?php

namespace ChaosTangent\FansubEbooks\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     */
    private $id;
    /**
     * @ORM\Column(type="text")
     */
    private $line;
    /**
     * @ORM\Column(name="charactercount",type="integer")
     */
    private $characterCount;
    /**
     * @ORM\ManyToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\File", inversedBy="lines")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $file;
    /**
     * @ORM\OneToMany(targetEntity="ChaosTangent\FansubEbooks\Entity\Vote", mappedBy="line")
     */
    private $votes;
    /**
     * @ORM\OneToMany(targetEntity="ChaosTangent\FansubEbooks\Entity\Flag", mappedBy="line")
     */
    private $flags;
    /**
     * @ORM\OneToMany(targetEntity="ChaosTangent\FansubEbooks\Entity\Tweet", mappedBy="line")
     */
    private $tweets;

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
     * Add tweets
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Tweet $tweets
     * @return Line
     */
    public function addTweet(\ChaosTangent\FansubEbooks\Entity\Tweet $tweets)
    {
        $this->tweets[] = $tweets;

        return $this;
    }

    /**
     * Remove tweets
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Tweet $tweets
     */
    public function removeTweet(\ChaosTangent\FansubEbooks\Entity\Tweet $tweets)
    {
        $this->tweets->removeElement($tweets);
    }

    /**
     * Get tweets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTweets()
    {
        return $this->tweets;
    }
}
