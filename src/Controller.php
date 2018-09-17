<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Vlad Riabchenko <contact@vria.eu>
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
     * Default action to execute on every request.
     *
     * @return Response
     */
    public function defaultAction()
    {
        $token = $this->tokenStorage->getToken();

        return new Response(
            'Request uri: '.$this->request->getRequestUri().'<br>'
            .'Token: '.(is_object($token) ? get_class($token) : gettype($token)).'<br>'
            .'Username: '.($token ? $token->getUsername(): 'NULL')
        );
    }
}
