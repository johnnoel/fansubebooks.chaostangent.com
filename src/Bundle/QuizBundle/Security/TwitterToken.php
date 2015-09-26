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
    protected $callbackUri;

    public function __construct($providerKey, $callbackUri, array $roles = [])
    {
        parent::__construct($roles);

        $this->providerKey = $providerKey;
        $this->callbackUri = $callbackUri;

        parent::setAuthenticated(count($roles) > 0);
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

    public function getCallbackUri()
    {
        return $this->callbackUri;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array($this->providerKey, $this->callbackUri, parent::serialize()));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->providerKey, $this->callbackUri, $parentStr) = unserialize($serialized);
        parent::unserialize($parentStr);
    }

    public function getCredentials()
    {
        return '';
    }
}
