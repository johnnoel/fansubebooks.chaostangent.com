<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Twitter security token
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class TwitterToken extends AbstractToken
{
    /** @var string */
    protected $providerKey;
    /** @var string */
    protected $oauthToken;
    /** @var string */
    protected $oauthVerifier;

    public function __construct($providerKey, $oauthToken = '', $oauthVerifier = '', array $roles = [])
    {
        parent::__construct($roles);

        $this->providerKey = $providerKey;
        $this->oauthToken = $oauthToken;
        $this->oauthVerifier = $oauthVerifier;

        parent::setAuthenticated(!empty($oauthToken) && !empty($oauthVerifier));
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthenticated($isAuthenticated)
    {
        if ($isAuthenticated) {
            throw new \LogicException('Cannot set this token to trusted after instantiation.');
        }

        parent::setAuthenticated(false);
    }

    /**
     * Returns the provider key.
     *
     * @return string The provider key
     */
    public function getProviderKey()
    {
        return $this->providerKey;
    }

    /**
     * Returns the OAuth token from the second stage of OAuth auth
     *
     * @return string
     */
    public function getOauthToken()
    {
        return $this->oauthToken;
    }

    /**
     * Returns the OAuth verifier from the second stage of OAuth auth
     *
     * @return string
     */
    public function getOauthVerifier()
    {
        return $this->oauthVerifier;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array($this->providerKey, $this->oauthToken, $this->oauthVerifier, parent::serialize()));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->providerKey, $this->oauthToken, $this->oauthVerifier, $parentStr) = unserialize($serialized);
        parent::unserialize($parentStr);
    }
}
