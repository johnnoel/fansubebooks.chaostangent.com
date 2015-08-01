<?php

namespace ChaosTangent\FansubEbooks\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * File entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @ORM\Entity(repositoryClass="ChaosTangent\FansubEbooks\Entity\Repository\FileRepository")
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
     * @Serializer\ReadOnly
     * @Serializer\Type("integer")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="ChaosTangent\FansubEbooks\Entity\Series", inversedBy="files")
     * @ORM\JoinColumn(name="series_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @Serializer\Exclude
     */
    private $series;
    /**
     * @ORM\Column(type="text")
     * @Serializer\Type("string")
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=64)
     * @Serializer\Type("string")
     */
    private $hash;
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Type("DateTime<'D, d M Y H:i:s O', 'UTC'>")
     */
    private $added;
    /**
     * @ORM\OneToMany(targetEntity="ChaosTangent\FansubEbooks\Entity\Line", mappedBy="file", cascade="all")
     * @Serializer\Type("ArrayCollection<ChaosTangent\FansubEbooks\Entity\Line>")
     * @Serializer\MaxDepth(2)
     */
    private $lines;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lines = new ArrayCollection();
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
     * Add line
     *
     * @param \ChaosTangent\FansubEbooks\Entity\Line $line
     * @return File
     */
    public function addLine(\ChaosTangent\FansubEbooks\Entity\Line $line)
    {
        $line->setFile($this);
        $this->lines[] = $line;

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

    /**
     * Set lines
     *
     * @param array|ArrayCollection $lines
     * @return File
     */
    public function setLines($lines)
    {
        if (is_array($lines)) {
            $lines = new ArrayCollection($lines);
        }

        if (!($lines instanceof ArrayCollection)) {
            throw new \InvalidArgumentException('$lines must be an array or an ArrayCollection');
        }

        $this->lines = $lines;

        return $this;
    }
}
