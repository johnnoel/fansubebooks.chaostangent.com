<?php

namespace ChaosTangent\FansubEbooks\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Line entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @ORM\Entity
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
}
