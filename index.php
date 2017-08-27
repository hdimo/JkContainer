<?php 

use Jk\Container\Container;

require 'vendor/autoload.php';

class MyClass 
{
    public function __construct(){

    }
}

$services = [
    MyClass::class => [
        "class"=>MyClass::class,
    ]
];

$container = new Container($services, []);

$container->get(MyClass::class);
//var_dump();


