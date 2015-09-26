<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\Security;

use Symfony\Component\Security\Http\Firewall\ListenerInterface,
    Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface,
    Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * Twitter authentication listener
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class TwitterAuthenticationListener implements ListenerInterface
{
    /** @var TokenStorageInterface */
    protected $tokenStorage;
    /** @var AuthenticationManagerInterface */
    protected $authenticationManager;
    /** @var HttpUtils */
    protected $httpUtils;
    /** @var string */
    protected $providerKey;

    /** @var string */
    protected $loginPath;
    /** @var string */
    protected $confirmPath;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager, HttpUtils $httpUtils, $providerKey, $loginPath, $confirmPath)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->httpUtils = $httpUtils;
        $this->providerKey = $providerKey;

        $this->loginPath = $loginPath;
        $this->confirmPath = $confirmPath;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->hasSession()) {
            throw new \RuntimeException('This authentication method requires a session.');
        }

        if (!$this->requiresRedirect($request) && !$this->requiresAuthentication($request)) {
            return;
        }

        $token = null;

        if ($this->requiresAuthentication($request)) {
            $token = new TwitterToken(
                $this->providerKey,
                $request->query->get('oauth_token', ''),
                $request->query->get('oauth_verifier', '')
            );
        } else {
            $token = new TwitterToken($this->providerKey);
        }

        return $this->authenticationManager->authenticate($token);
    }

    protected function requiresRedirect(Request $request)
    {
        return $this->httpUtils->checkRequestPath($request, $this->loginPath);
    }

    protected function requiresAuthentication(Request $request)
    {
        return $this->httpUtils->checkRequestPath($request, $this->confirmPath);
    }
}
