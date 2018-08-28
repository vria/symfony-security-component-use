<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class ControllerResolver implements ControllerResolverInterface
{
    /** @var callable */
    private $default;

    /**
     * @param callable $default
     */
    public function __construct(callable $default)
    {
        $this->default = $default;
    }

    /**
     * @inheritdoc
     */
    public function getController(Request $request)
    {
        return $this->default;
    }
}
