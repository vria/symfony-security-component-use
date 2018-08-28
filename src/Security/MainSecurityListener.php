<?php

namespace App\Security;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class MainSecurityListener implements ListenerInterface
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var AuthenticationManagerInterface */
    private $authenticationManager;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param AuthenticationManagerInterface $authenticationManager
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * @inheritdoc
     */
    public function handle(GetResponseEvent $event)
    {
        // Extract authentication credentials.
        $request = $event->getRequest();
        $username = $request->query->get('auth_user');
        $credentials = $request->query->get('auth_pw');

        if ($username && $credentials) {
            try {
                // Token is not authenticated because no role is passed.
                $token = new UsernamePasswordToken($username, $credentials, 'main');

                // Try to authenticate the token.
                // If there is an authentication error an AuthenticationException is thrown.
                $token = $this->authenticationManager->authenticate($token);

                // Add authenticated token to storage.
                $this->tokenStorage->setToken($token);
            } catch (AuthenticationException $e) {
            }
        }
    }
}
