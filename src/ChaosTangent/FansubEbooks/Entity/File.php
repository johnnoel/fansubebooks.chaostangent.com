<?php

namespace ChaosTangent\FansubEbooks\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * File entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @ORM\Entity
 * @ORM\Table(name="files",
 *      indexes={@ORM\Index(name="hash_idx", columns={"hash"})}
 * )
 */
class File
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\Series", inversedBy="files")
     * @ORM\JoinColumn(name="series_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $series;
    /**
     * @ORM\Column(type="text")
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=64)
     */
    private $hash;
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $added;
    /**
     * @ORM\OneToMany(targetEntity="ChaosTangent\FansubEbooks\Entity\Line", mappedBy="file")
     */
    private $lines;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lines = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return File
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set added
     *
     * @param \DateTime $added
     * @return File
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
     * Set series
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Series $series
     * @return File
     */
    public function setSeries(\ChaosTangent\FansubEbooks\Entity\Series $series)
    {
        $this->series = $series;

        return $this;
    }

    /**
     * Get series
     *
     * @return \ChaosTangent\FansubEbooks\Entity\Series
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * Add lines
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Line $lines
     * @return File
     */
    public function addLine(\ChaosTangent\FansubEbooks\Entity\Line $lines)
    {
        $this->lines[] = $lines;

        return $this;
    }

    /**
     * Remove lines
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Line $lines
     */
    public function removeLine(\ChaosTangent\FansubEbooks\Entity\Line $lines)
    {
        $this->lines->removeElement($lines);
    }

    /**
     * Get lines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLines()
    {
        return $this->lines;
    }
}
