<?php
/**
 * Created by PhpStorm.
 * User: khaled
 * Date: 8/28/17
 * Time: 12:56 PM
 */

namespace JkTest\Container\ArgumentResolver;

use Jk\Container\Resolver\ArgumentResolver;
use PHPUnit\Framework\TestCase;

class ArgumentResolverTest extends TestCase
{

    private $argumentResolver;

    public function setUp(){

        $parameter = ["mail.transport"=>"smtp"];

        $this->argumentResolver = new ArgumentResolver($parameter);
    }

    public function testResolve(){

        $out = [
            'mail'=>[
                "transport"=>"mail"
            ]
        ];
        $result = $this->argumentResolver->resolve();

        $this->assertEquals($out, $result);

    }

}