<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\Security;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Twitter authentication listener
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class TwitterAuthenticationListener extends AbstractAuthenticationListener
{
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

        return $this->authenticationManager->authenticate($token);
    }
}
