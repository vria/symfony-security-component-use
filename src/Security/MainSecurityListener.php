<?php

namespace App\Security;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class MainSecurityListener implements ListenerInterface
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var UserProviderInterface */
    private $userProvider;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param UserProviderInterface $userProvider
     */
    public function __construct(TokenStorageInterface $tokenStorage, UserProviderInterface $userProvider)
    {
        $this->tokenStorage = $tokenStorage;
        $this->userProvider = $userProvider;
    }

    /**
     * @inheritdoc
     */
    public function handle(GetResponseEvent $event)
    {
        // Extract credentials from request.
        $request = $event->getRequest();
        $username = $request->query->get('auth_user');
        $password = $request->query->get('auth_pw');

        try {
            // try to load a user object by username passed in request.
            $user = $this->userProvider->loadUserByUsername($username);

            if ($user->getPassword() === $password) {
                // Create token is credentials are valid.
                $token = new UsernamePasswordToken($user, $password, 'main', $user->getRoles());
                $this->tokenStorage->setToken($token);
            }
        } catch (UsernameNotFoundException $e) {
        }
    }
}
