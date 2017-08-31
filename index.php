<?php 

use Jk\Container\Container;

require 'vendor/autoload.php';


class MyClass
{
    private $string;
    private $parameters;
    public function __construct($string, $param){
        $this->string = $string;
        $this->parameters = $param;
    }

    public function getParameter($name)
    {
        $token = explode('.', $name);
        $context = $this->parameters;
        while (null !== ($token = array_shift($token))) {
            if (!isset($context[$token])) {
                throw new ParameterNotFoundException("Parameter not found " . $name);
            }
            $context = $context[$token];
        }
        return $context;
    }
}



$pm = [
    "mail"=>[
        "transport"=>"smtp"
    ]
];

$mc = new MyClass("_name", $pm);

$s = $mc->getParameter("mail.trasport");

/*
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


*/