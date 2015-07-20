<?php

namespace ChaosTangent\FansubEbooks\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Flag entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @ORM\Entity
 * @ORM\Table(name="flags")
 */
class Flag
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
     * @return Flag
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
     * @return Flag
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
     * Set line
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Line $line
     * @return Flag
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
