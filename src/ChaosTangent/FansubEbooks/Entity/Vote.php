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
}
