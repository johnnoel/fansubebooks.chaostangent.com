<?php

namespace ChaosTangent\FansubEbooks\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tweet entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @ORM\Entity(repositoryClass="ChaosTangent\FansubEbooks\Entity\Repository\TweetRepository")
 * @ORM\Table(name="tweets", indexes={
 *      @ORM\Index(name="tweet_id_idx", columns={"tweet_id"})
 * })
 */
class Tweet
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\Line", inversedBy="tweets")
     * @ORM\JoinColumn(name="line_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $line;
    /**
     * @ORM\Column(type="datetime")
     */
    private $tweeted;
    /**
     * @ORM\Column(type="string", length=20, name="tweet_id", nullable=true)
     */
    private $tweetId;

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
     * Set tweeted
     *
     * @param \DateTime $tweeted
     * @return Tweet
     */
    public function setTweeted($tweeted)
    {
        $this->tweeted = $tweeted;

        return $this;
    }

    /**
     * Get tweeted
     *
     * @return \DateTime
     */
    public function getTweeted()
    {
        return $this->tweeted;
    }

    /**
     * Set tweetId
     *
     * @param string $tweetId
     * @return Tweet
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
     * Set line
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Line $line
     * @return Tweet
     */
    public function setLine(\ChaosTangent\FansubEbooks\Entity\Line $line = null)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line
     *
     * @return \ChaosTangent\FansubEbooks\Entity\Line
     */
    public function getLine()
    {
        return $this->line;
    }
}
