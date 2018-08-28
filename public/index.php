<?php

/**
 * @author Vlad Riabchenko <contact@vria.eu>
 */

require_once "../vendor/autoload.php";

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals(); // HTTP request
$tokenStorage = new TokenStorage(); // Service that stores user token

// Call security listener on every request.
$securityListener = new \App\Security\SecurityListener($tokenStorage);
$securityListener->onRequest($request);

// Any code you can imagine to generate a response.
// You can deny access if no token were set.
$token = $tokenStorage->getToken();
$response = new Response(
    'Request uri: '.$request->getRequestUri().'<br>'
    .'Token: '.(is_object($token) ? get_class($token) : gettype($token)).'<br>'
    .'Username: '.($token ? $token->getUsername(): 'NULL')
);

$response->send(); // Send response
