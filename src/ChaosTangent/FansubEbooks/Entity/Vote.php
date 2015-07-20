<?php

namespace ChaosTangent\FansubEbooks\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Vote entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @ORM\Entity
 * @ORM\Table(name="votes")
 */
class Vote
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\Line", inversedBy="votes")
     * @ORM\JoinColumn(name="line_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $line;
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $added;
    /**
     * NOTE: non-portable columnDefinition, Postgres specific
     * @ORM\Column(type="string", length=39, columnDefinition="inet")
     */
    private $ip;
    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $positive = false;

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
     * Set added
     *
     * @param \DateTime $added
     * @return Vote
     */
    public function setAdded($added)
    {
        $this->added = $added;

        return $this;
    }

    /**
     * Get added
     *
     * @return \DateTime 
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Vote
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set positive
     *
     * @param boolean $positive
     * @return Vote
     */
    public function setPositive($positive)
    {
        $this->positive = $positive;

        return $this;
    }

    /**
     * Get positive
     *
     * @return boolean 
     */
    public function getPositive()
    {
        return $this->positive;
    }

    /**
     * Set line
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Line $line
     * @return Vote
     */
    public function setLine(\ChaosTangent\FansubEbooks\Entity\Line $line)
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
