<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Quiz user entity
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @ORM\Entity(repositoryClass="ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="quiz_users")
 */
class User implements UserInterface
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
     * @ORM\Column(type="string", length=255, name="display_name")
     */
    private $displayName;
    /**
     * @ORM\Column(type="text")
     */
    private $avatar;
    /**
     * @ORM\Column(type="datetime", name="signed_up")
     * @Gedmo\Timestampable(on="create")
     */
    private $signedUp;
    /**
     * @ORM\OneToMany(targetEntity="ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Answer", mappedBy="user")
     */
    private $answers;
    /**
     */
    private $correct = 0;
    private $incorrect = 0;
    private $skipped = 0;

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
     * Set displayName
     *
     * @param string $displayName
     * @return User
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername()
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
     * {@inheritDoc}
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return [ 'ROLE_QUIZ' ];
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * Set correct
     *
     * @param integer $correct
     * @return User
     */
    public function setCorrect($correct)
    {
        $this->correct = $correct;

        return $this;
    }

    /**
     * Get correct
     *
     * @return integer
     */
    public function getCorrect()
    {
        return $this->correct;
    }

    /**
     * Set incorrect
     *
     * @param integer $incorrect
     * @return User
     */
    public function setIncorrect($incorrect)
    {
        $this->incorrect = $incorrect;

        return $this;
    }

    /**
     * Get incorrect
     *
     * @return integer
     */
    public function getIncorrect()
    {
        return $this->incorrect;
    }

    /**
     * Set skipper
     *
     * @param integer $skipper
     * @return User
     */
    public function setSkipped($skipper)
    {
        $this->skipper = $skipper;

        return $this;
    }

    /**
     * Get skipper
     *
     * @return integer
     */
    public function getSkipped()
    {
        return $this->skipper;
    }
}
