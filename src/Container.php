<?php 
namespace Jk\Container;

use Interop\Container\ContainerInterface as InteropContainerInterface;
use Jk\Exception\ServiceNotFoundException;
use Jk\Exception\ContainerException;

class Container 
    implements InteropContainerInterface
{

    private $services;
    private $parameter;
    private $serviceStore;

    public function __construct(
        array $services,
        array $parameters
    ){

        $this->services = $services;
        $this->parameters = $parameters;
        $this->serviceStore = [];
    }

    public function get($name)
    {
        if($this->has($name)){
            throw new ServiceNotFoundException("Service not found".$name);            
        }

        if(!isset($this->serviceStore[$name])){
            $this->serviceStore[$name] = $this->createService($name);
        }

        return $this->serviceStore[$name];
    }

    public function getParameter($name){
        $token = explode('.', $name);
        $context = $this->parameters;
        while(null !== ($token = array_shift($token))){
            if(!isset($context[$token])){
                throw new ParameterNotFoundException("Parameter not found ".$name);                 
            }
            $context = $context[$token];
        }
        return $context;
    }

    public function has($name)
    {
        return isset($this->services[$name]);
    }

    private function createService($name){

        $entry = &$this->services[$name];

        var_dump($entry);

        if(!is_array($entry) || !isset($entry['class'])){
            throw new ContainerException("%s service entry must be an array containing a 'class' key", $name);
        }elseif(!class_exists($entry['class'])){
            throw new ContainerException('%s service class does not exist : %s',
                $name, 
                $entry['class']);
        }elseif(isset($entry['lock'])){
            throw new ContainerException("%s service contains a circular reference", $name);
        }

        $entry['lock'] = true;
        $arguments = isset($entry['arguments']) ?
                        $this->resolveArguments($name, $entry['arguments']):
                        [];
        $reflector = new \ReflectionClass($entry['class']);

        $service = $reflector->newInstanceArgs($arguments);

        if(isset(entry['calls'])){
            $this->initializeService(
                $service, 
                $name,
                $entry['calls']
            );
        }
        return $service;
    }

    private function resolveArguments($name, array $argumentDefinitions){

        $arguments = [];
        foreach ($argumentDefinitions as $argumentDefinition) {
            if($argumentDefinition instanceof ServiceReference){
                $argumentServiceName = $argumentDefinition->getName();

                $arguments[] = $this->get($argumentServiceName);
            }elseif($argumentDefinition instanceof ParameterReference){
                $argumentParameterName = $argumentDefinition->getName();
                $arguments[] = $this->getParameter($argumentDefinition);
            }
        }
        return $arguments;
    }


    private function initializeService($service, $name, array $callDefinitions)
    {
        foreach ($callDefinitions as $callDefinition) {
            if(!is_array($callDefinition) || !isset($callDefinition['method'])){
                throw new ContainerException($name.' service calls must be arrays containing a \'method\' key');
            }elseif (!is_callable([$service, $callDefinition['method']])) {
                throw new ContainerException($name.' service asks for call to uncallable method: '.$callDefinition['method']);
            }

            $arguments = isset($callDefinition['arguments']) ?
                            $this->resolveArguments($name, $callDefinition['arguments']):
                            [];
            call_user_func_array([
                $service, 
                $callDefinition['method']], 
                $arguments
            );
        }
    }

}