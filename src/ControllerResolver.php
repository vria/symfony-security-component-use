<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

/**
 * @see ControllerResolverInterface for more information about controller resolvers.
 *
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class ControllerResolver implements ControllerResolverInterface
{
    /** @var callable[] */
    private $routes;

    /** @var callable */
    private $default;

    /**
     * @param callable[] $routes
     * @param callable $default
     */
    public function __construct(array $routes, callable $default)
    {
        $this->routes = $routes;
        $this->default = $default;
    }

    /**
     * @inheritdoc
     */
    public function getController(Request $request)
    {
        foreach ($this->routes as $pattern => $controller) {
            if (preg_match($pattern, $request->getPathInfo())) {
                return $controller;
            }
        }

        return $this->default;
    }
}
