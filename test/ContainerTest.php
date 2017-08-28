<?php
namespace JkTest\Container;

use PHPUnit\Framework\TestCase;

use Jk\Container\Container;


class ContainerTest extends TestCase
{
    private $service;
    private $parameters;
    private $container;

    public function setUp(){
        $this->service = [

        ];
        $this->parameters = [
            "password"=>"pass"
        ];

        $this->container = new Container($this->service,  $this->parameters);
    }

    /**
     * @expectedException Jk\Container\Exception\ContainerException
     */
    public function testCeateServiceContainerExceptionWithoutClassKey(){
        $services = ["myObject"=>new \stdClass()];
        $container = new Container($services, []);
        $container->get("myObject");
    }

    /**
     * @expectedException Jk\Container\Exception\ServiceNotFoundException
     */
    public function testServiceNotFoundException(){
        $service = [];
        $parameter = [];
        $container = new Container($service, $parameter);
        $container->get("someservice");
    }


}