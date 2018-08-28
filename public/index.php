<?php

/**
 * @author Vlad Riabchenko <contact@vria.eu>
 */

require_once "../vendor/autoload.php";

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\AccessMap;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\FirewallMap;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Symfony\Component\Security\Http\Firewall\BasicAuthenticationListener;
use Symfony\Component\Security\Http\Firewall\AccessListener;
use Symfony\Component\Security\Http\Firewall\ContextListener;
use Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategy;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Http\EntryPoint\BasicAuthenticationEntryPoint;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\HttpKernel\KernelEvents;

$request = Request::createFromGlobals(); // HTTP request.
$session = new Session();
$request->setSession($session);
$tokenStorage = new TokenStorage(); // Service that stores user token.
$dispatcher = new EventDispatcher();
$httpUtils = new HttpUtils();

// Controller creates a response to send to the user.
$controller = new \App\Controller($request, $tokenStorage);
$controllerResolver = new \App\ControllerResolver(
    ['/^\/front\/login$/' => [$controller, 'loginFormAction']],
    [$controller, 'defaultAction']
);

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

// Entry point helps user to authenticate.
// In the case of HTTP basic authentication it returns 401 response to invite user to enter its credentials.
$basicAuthenticationEntryPoint = new BasicAuthenticationEntryPoint('Secured area');

// Create HTTP basic security listener that extracts credentials from headers (RFC 7617).
$mainSecurityListener = new BasicAuthenticationListener($tokenStorage, $mainAuthProvider, 'main', $basicAuthenticationEntryPoint);

// Access listener will throw an exception when no token is already present.
$accessDecisionManager = new AccessDecisionManager();
$accessMap = new AccessMap();
$accessListener = new AccessListener($tokenStorage, $accessDecisionManager, $accessMap, $mainAuthProvider);

// Create firewall map and add main security listener under URLs starting with "/main".
$firewallMap = new FirewallMap();
$firewallMap->add(new RequestMatcher('^/main'), [$mainSecurityListener, $accessListener]);

// ContextListener retrieves previously authenticated token from the session during REQUEST event.
// It also saves token during RESPONSE event.
$contextListener = new ContextListener($tokenStorage, [$mainUserProvider], 'front', null, $dispatcher);

$sessionAuthenticationStrategy = new SessionAuthenticationStrategy(SessionAuthenticationStrategy::MIGRATE);
$successHandler = new DefaultAuthenticationSuccessHandler($httpUtils, ['default_target_path' => '/front/success']);
$failureHandler = new DefaultAuthenticationFailureHandler($kernel, $httpUtils, ['login_path' => '/front/login']);

// Exactly the same authentication provider as for HTTP basic except '$providerKey'.
$frontAuthProvider = new DaoAuthenticationProvider($mainUserProvider, $mainUserChecker, 'front', $encoderFactory);

// Listens for login form being send (POST to '/front/login_check').
// It extracts credentials, creates token, authenticates it and puts it to the token storage.
$formAuthListener = new UsernamePasswordFormAuthenticationListener(
    $tokenStorage,
    $frontAuthProvider,
    $sessionAuthenticationStrategy,
    $httpUtils,
    'front',
    $successHandler, // Redirect user to '/front/success' if credentials are valid
    $failureHandler, // Redirect user to '/front/login' if credentials are invalid
    [
        'check_path' => '/front/login_check',
        'post_only' => true,
    ] // Act only on POST to '/front/login_check'
);

$firewallMap->add(new RequestMatcher('^/front'), [$contextListener, $formAuthListener]);

// Create firewall and add it to dispatcher.
$firewall = new Firewall($firewallMap, $dispatcher);
$dispatcher->addSubscriber($firewall);

// ExceptionListener catches authentication exception and converts them to Response instance.
// In this case it invites user to enter its credentials by returning 401 response.
$authTrustResolver = new AuthenticationTrustResolver(AnonymousToken::class, RememberMeToken::class);

$exceptionListener = new ExceptionListener($tokenStorage, $authTrustResolver, $httpUtils, 'main', $basicAuthenticationEntryPoint);
$dispatcher->addListener(KernelEvents::EXCEPTION, array($exceptionListener, 'onKernelException'), 1);

$response = $kernel->handle($request); // Launch kernel and retrieve response.
$response->send(); // Send response.
