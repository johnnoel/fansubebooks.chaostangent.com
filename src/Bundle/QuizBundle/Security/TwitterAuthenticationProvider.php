<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\Security;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface,
    Symfony\Component\HttpFoundation\RedirectResponse;
use League\OAuth1\Client\Server\Twitter;

/**
 * Twitter authentication provider
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class TwitterAuthenticationProvider implements AuthenticationProviderInterface
{
    /** @var UserProviderInterface */
    protected $userProvider;
    /** @var SessionInterface */
    protected $session;
    /** @var string */
    protected $providerKey;
    /** @var Twitter */
    protected $twitter;

    public function __construct(UserProviderInterface $userProvider, SessionInterface $session, $providerKey, $consumerKey, $consumerSecret)
    {
        $this->userProvider = $userProvider;
        $this->session = $session;
        $this->providerKey = $providerKey;

        $this->twitter = new Twitter([
            'identifier' => $consumerKey,
            'secret' => $consumerSecret,
            'callback_uri' => '',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function supports(TokenInterface $token)
    {
        return ($token instanceof TwitterToken) &&
            ($token->getProviderKey() == $this->providerKey);
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$token->isAuthenticated()) {
            $temp = $this->twitter->getTemporaryCredentials();
            $this->session->set('twitter_oauth_credentials', serialize($temp));

            $redirectUri = $this->twitter->getAuthorizationUrl($temp);

            return new RedirectResponse($redirectUri);
        }

        // authenticate
        $temp = unserialize($this->session->get('twitter_oauth_credentials'));
        $credentials = $this->twitter->getTokenCredentials(
            $temp,
            $token->getOauthToken(),
            $token->getOauthVerifier()
        );

        $user = $this->twitter->getUserDetails($credentials);

        return $this->userProvider->loadUserByUsername($user);
    }
}
