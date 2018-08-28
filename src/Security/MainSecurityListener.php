<?php

namespace App\Security;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class MainSecurityListener implements ListenerInterface
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @inheritdoc
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $user = $request->query->get('auth_user');
        $password = $request->query->get('auth_pw');

        if ($user === 'vlad' && $password === 'pass') {
            $token = new UsernamePasswordToken($user, $password, 'main', ['ROLE_USER']);
            $this->tokenStorage->setToken($token);
        }
    }
}
