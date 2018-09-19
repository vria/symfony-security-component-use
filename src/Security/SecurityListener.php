<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class SecurityListener
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
     * @param Request $request
     */
    public function onRequest(Request $request)
    {
        // Extract credentials from request
        $user = $request->query->get('auth_user');
        $password = $request->query->get('auth_pw');

        if ($user === 'gordon' && $password === 'freeman') {
            // Credentials are valid.
            // Create a token with user object, credentials, provider key and roles
            $token = new UsernamePasswordToken($user, $password, 'main', ['ROLE_USER']);

            // Save it to token storage
            $this->tokenStorage->setToken($token);
        }
    }
}
