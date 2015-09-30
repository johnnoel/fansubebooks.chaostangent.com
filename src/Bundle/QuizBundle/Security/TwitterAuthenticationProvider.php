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
    const SESSION_KEY = 'twitter_oauth_credentials';

    /** @var UserProviderInterface */
    protected $userProvider;
    /** @var SessionInterface */
    protected $session;
    /** @var string */
    protected $providerKey;
    /** @var string */
    protected $consumerKey;
    /** @var string */
    protected $consumerSecret;

    public function __construct(UserProviderInterface $userProvider, SessionInterface $session, $providerKey, $consumerKey, $consumerSecret)
    {
        $this->userProvider = $userProvider;
        $this->session = $session;
        $this->providerKey = $providerKey;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
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
        $this->twitter = new Twitter([
            'identifier' => $this->consumerKey,
            'secret' => $this->consumerSecret,
            'callback_uri' => $token->getCallbackUri(),
        ]);

        if ($token->hasAttribute('oauth_token') && $token->hasAttribute('oauth_verifier')) {
            // authenticate
            $temp = unserialize($this->session->get(self::SESSION_KEY));
            $credentials = $this->twitter->getTokenCredentials(
                $temp,
                $token->getAttribute('oauth_token'),
                $token->getAttribute('oauth_verifier')
            );

            $twitterUser = $this->twitter->getUserDetails($credentials);

            $user = $this->userProvider->loadUserByUsername($twitterUser);

            $authToken = new TwitterToken($token->getProviderKey(), '', $user->getRoles());
            $authToken->setUser($user);

            return $authToken;
        }

        $temp = $this->twitter->getTemporaryCredentials();
        $this->session->set(self::SESSION_KEY, serialize($temp));

        $redirectUri = $this->twitter->getAuthorizationUrl($temp);

        return new RedirectResponse($redirectUri);
    }
}