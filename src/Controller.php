<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class Controller
{
    /** @var Request */
    private $request;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(Request $request, TokenStorageInterface $tokenStorage)
    {
        $this->request = $request;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return Response
     */
    public function loginFormAction()
    {
        return new Response(<<<END
<form action="/front/login_check" method="POST">
    <input type="text" name="_username" placeholder="username">
    <input type="password" name="_password" placeholder="password">
    <input type="submit">
</form>
END
        );
    }

    /**
     * @return Response
     */
    public function defaultAction()
    {
        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;

        return new Response(
            'Request uri: '.$this->request->getRequestUri().'<br>'
            .'Token: '.(is_object($token) ? get_class($token) : gettype($token)).'<br>'
            .'Username: '.($token ? $token->getUsername(): 'NULL').'<br>'
            .'User class: '.(is_object($user) ? get_class($user): gettype($user))
        );
    }
}
