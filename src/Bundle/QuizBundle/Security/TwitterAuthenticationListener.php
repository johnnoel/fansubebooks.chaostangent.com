<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\Security;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Twitter authentication listener
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class TwitterAuthenticationListener extends AbstractAuthenticationListener
{
    /** @var AuthenticationProviderInterface */
    protected $authProvider;

    /**
     * {@inheritDoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        $token = new TwitterToken(
            $this->providerKey,
            $this->httpUtils->generateUri($request, $this->options['check_path'])
        );

        if ($request->query->has('oauth_token') && $request->query->has('oauth_verifier')) {
            // leg 2, set things
            $token->setAttribute('oauth_token', $request->query->get('oauth_token', ''));
            $token->setAttribute('oauth_verifier', $request->query->get('oauth_verifier', ''));
        } else if ($request->query->has('error')) {
            throw new AuthenticationException('User denied access to app');
        }

        /**
         * @see setAuthProvider
         */
        if ($this->authProvider !== null) {
            return $this->authProvider->authenticate($token);
        }

        return $this->authenticationManager->authenticate($token);
    }

    /**
     * Set the direct authentication provider
     *
     * Why not use $authenticationManager you ask? Well you ALWAYS have to
     * return a TokenInterface from authenticate because the manager just
     * blithely calls methods and whatnot on it without checking. Which means
     * I either need to modify the MASSIVE constructor for
     * AbstractAuthenticationListener or do it this way
     *
     * @param AuthenticationProviderInterface $provider
     */
    public function setAuthProvider(AuthenticationProviderInterface $provider)
    {
        $this->authProvider = $provider;
    }
}
