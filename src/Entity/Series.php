<?php

namespace ChaosTangent\FansubEbooks\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Series entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @ORM\Entity(repositoryClass="ChaosTangent\FansubEbooks\Entity\Repository\SeriesRepository")
 * @ORM\Table(name="series")
 */
class Series
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
    private $title;
    /**
     * @ORM\Column(type="string", length=128, unique=true)
     * @Gedmo\Slug(fields={"title"})
     * @Serializer\Type("string")
     */
    private $alias;
    /**
     * @ORM\Column(type="text")
     * @Serializer\Type("string")
     */
    private $image;
    /**
     * @ORM\Column(type="text")
     * @Serializer\Type("string")
     */
    private $thumbnail;
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Type("DateTime<'D, d M Y H:i:s O', 'UTC'>")
     */
    private $added;
    /**
     * @ORM\OneToMany(targetEntity="ChaosTangent\FansubEbooks\Entity\File", mappedBy="series")
     * @ORM\OrderBy({ "name": "ASC" })
     * @Serializer\Exclude
     */
    private $files;
    /**
     * @var \DateTime
     * @Serializer\Type("DateTime<'D, d M Y H:i:s O', 'UTC'>")
     */
    private $updated;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Series
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Series
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Series
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set thumbnail
     *
     * @param string $thumbnail
     * @return Series
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set added
     *
     * @param \DateTime $added
     * @return Series
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return Series
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Add files
     *
     * @param \ChaosTangent\FansubEbooks\Entity\File $files
     * @return Series
     */
    public function addFile(\ChaosTangent\FansubEbooks\Entity\File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param \ChaosTangent\FansubEbooks\Entity\File $files
     */
    public function removeFile(\ChaosTangent\FansubEbooks\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
    }
}
