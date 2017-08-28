<?php
namespace JkTest\Container;

use PHPUnit\Framework\TestCase;

use Jk\Container\Container;
use Jk\Container\Exception\ServiceNotFoundException;

class ContainerTest extends TestCase
{

    private $service;

    public function setUp(){
        $this->service = [];

    }

    /**
     * @expectedException \Exception
     */
    public function testServiceNotFoundException(){
        $service = [];
        $parameter = [];
        $container = new Container($service, $parameter);
        $container->get("someservice");
    }


}