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
use Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider;
use Symfony\Component\Security\Http\FirewallMap;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Http\Firewall\AnonymousAuthenticationListener;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

$request = Request::createFromGlobals(); // HTTP request.
$tokenStorage = new TokenStorage(); // Service that stores user token.
$dispatcher = new EventDispatcher();

// Controller creates a response to send to the user.
$controller = new \App\Controller($request, $tokenStorage);
$controllerResolver = new \App\ControllerResolver([$controller, 'defaultAction']);

// Kernel is in charge of converting a Request into a Response by using the event dispatcher.
$kernel = new HttpKernel($dispatcher, $controllerResolver);

// Create user provider that will be used by authentication listener.
$mainUserProvider = new InMemoryUserProvider([
    'vlad' => [
        'password' => '$2y$10$zDUW3BF4T5ZVloDZqp0SN.1Ic4DG3xfxHUel5DXWkkpvaP0G8qXnq', // encoded 'pass'
        'roles' => ['ROLE_USER'],
        'enabled' => true,
    ]
]);

// And object that checks whether a user is non-locked, enabled, not expired, etc.
$mainUserChecker = new UserChecker();

// A factory that specifies encoding algorithm to each user class.
$encoderFactory = new EncoderFactory([
    User::class => new BCryptPasswordEncoder(10)
]);

// Create a provider to which security listener will delegate an authentication.
// It uses a user provider to retrieve a user by username.
// Then it will verify credentials (encoded password).
$mainAuthProvider = new DaoAuthenticationProvider($mainUserProvider, $mainUserChecker, 'main', $encoderFactory);

// Create main security listener that handles authentication.
$mainSecurityListener = new \App\Security\MainSecurityListener($tokenStorage, $mainAuthProvider);

// Create a security listener that adds anonymous token if none is already present.
$anonymousAuthenticationProvider = new AnonymousAuthenticationProvider('secret');
$anonListener = new AnonymousAuthenticationListener($tokenStorage, 'secret', null, $anonymousAuthenticationProvider);

// Create firewall map and add main security listener under URLs starting with "/main".
$firewallMap = new FirewallMap();
$firewallMap->add(new RequestMatcher('^/main'), [$mainSecurityListener, $anonListener]);

// Create firewall and add it to dispatcher.
$firewall = new Firewall($firewallMap, $dispatcher);
$dispatcher->addSubscriber($firewall);

$response = $kernel->handle($request); // Launch kernel and retrieve response.
$response->send(); // Send response.
