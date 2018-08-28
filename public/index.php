<?php

/**
 * @author Vlad Riabchenko <contact@vria.eu>
 */

require_once "../vendor/autoload.php";

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Http\FirewallMap;
use Symfony\Component\Security\Http\Firewall;

$request = Request::createFromGlobals(); // HTTP request.
$tokenStorage = new TokenStorage(); // Service that stores user token.
$dispatcher = new EventDispatcher();

// Controller creates a response to send to the user.
$controller = new \App\Controller($request, $tokenStorage);
$controllerResolver = new \App\ControllerResolver([$controller, 'action']);

// Kernel is in charge of converting a Request into a Response by using the event dispatcher.
$kernel = new HttpKernel($dispatcher, $controllerResolver);

// Create main security listener that handles authentication.
$securityListener = new \App\Security\MainSecurityListener($tokenStorage);

// Create firewall map and add main security listener under URLs starting with "/main".
$firewallMap = new FirewallMap();
$firewallMap->add(new RequestMatcher('^/main'), [$securityListener]);

// Create firewall and add it to dispatcher.
$firewall = new Firewall($firewallMap, $dispatcher);
$dispatcher->addSubscriber($firewall);

$response = $kernel->handle($request); // Launch kernel and retrieve response.
$response->send(); // Send response.
