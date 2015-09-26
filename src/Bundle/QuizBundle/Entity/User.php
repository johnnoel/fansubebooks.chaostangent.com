<?php

namespace ChaosTangent\FansubEbooks\Entity\Quiz;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Quiz user entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @ORM\Entity
 * @ORM\Table(name="quiz_users")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=32, unique=true, name="twitter_id")
     */
    private $twitterId;
    /**
     * @ORM\Column(type="datetime", name="signed_up")
     */
    private $signedUp;
    /**
     * @ORM\Column(type="json_array")
     */
    private $tokens;
    /**
     * @ORM\OneToMany(targetEntity="ChaosTangent\FansubEbooks\Entity\Quiz\Answer", mappedBy="user")
     */
    private $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
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
     * Set twitterId
     *
     * @param string $twitterId
     * @return User
     */
    public function setTwitterId($twitterId)
    {
        $this->twitterId = $twitterId;

        return $this;
    }

    /**
     * Get twitterId
     *
     * @return string
     */
    public function getTwitterId()
    {
        return $this->twitterId;
    }

    /**
     * Set signedUp
     *
     * @param \DateTime $signedUp
     * @return User
     */
    public function setSignedUp($signedUp)
    {
        $this->signedUp = $signedUp;

        return $this;
    }

    /**
     * Get signedUp
     *
     * @return \DateTime
     */
    public function getSignedUp()
    {
        return $this->signedUp;
    }

    /**
     * Set tokens
     *
     * @param array $tokens
     * @return User
     */
    public function setTokens($tokens)
    {
        $this->tokens = $tokens;

        return $this;
    }

    /**
     * Get tokens
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }
}
