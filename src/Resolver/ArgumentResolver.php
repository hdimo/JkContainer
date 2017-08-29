<?php
/**
 * Created by PhpStorm.
 * User: khaled
 * Date: 8/28/17
 * Time: 1:29 PM
 */

namespace Jk\Container\Resolver;


class ArgumentResolver implements IResolver
{

    private $container;

    public function __construct($container, $argument)
    {
        $this->container = $container;
    }

    public function resolve()
    {

    }
}