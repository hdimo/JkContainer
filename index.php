<?php 

use Jk\Container\Container;

require 'vendor/autoload.php';

class MyClass 
{
    private $string;
    public function __construct($string){
        $this->string = $string;
    }
}

$services = [
    MyClass::class => [
        "class"=>MyClass::class,
        "argument"=>[
            "simple"
        ]
    ]
];

$container = new Container($services, []);

$srv = $container->get(MyClass::class);
var_dump($srv);


