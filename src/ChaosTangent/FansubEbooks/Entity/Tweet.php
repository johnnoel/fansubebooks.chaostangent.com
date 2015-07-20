<?php

namespace ChaosTangent\FansubEbooks\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tweet entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @ORM\Entity
 * @ORM\Table(name="tweets")
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
