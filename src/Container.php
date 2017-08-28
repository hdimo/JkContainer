<?php
namespace Jk\Container;

use Interop\Container\ContainerInterface as InteropContainerInterface;
use Jk\Container\Exception\ServiceNotFoundException;
use Jk\Container\Exception\ContainerException;

class Container
    implements InteropContainerInterface
{

    CONST ARGUMENTS_KEY = "arguments";
    CONST CLASS_KEY = "class";
    CONST CALL_KEY = "call";
    CONST LOCK_KEY = "call";

    private $services;
    private $parameter;
    private $serviceStore;

    public function __construct(
        array $services,
        array $parameters
    )
    {

        $this->services = $services;
        $this->parameters = $parameters;
        $this->serviceStore = [];
    }

    /**
     * @param string $name
     * @return mixed
     * @throws ServiceNotFoundException
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new ServiceNotFoundException(sprintf("%s Service not found", $name));
        }
        if (!isset($this->serviceStore[$name])) {
            $this->serviceStore[$name] = $this->createService($name);
        }
        return $this->serviceStore[$name];
    }

    /**
     * get parameter
     *
     * @param $name
     * @return array|mixed
     */
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

    /**
     * check if service exists
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->services[$name]);
    }

    /**
     * create service
     *
     * format [
     *  'serviceName'=> [
     *      'class'=> \Classname,
     *      'arguments'=> [] // if any (can be a parameter, value, or another service )
     *  ]
     * ]
     *
     * @param $name
     * @return object
     * @throws ContainerException
     */
    private function createService($name)
    {
        $entry = &$this->services[$name];
        if (!is_array($entry) || !isset($entry[self::CLASS_KEY])) {
            throw new ContainerException(sprintf("%s service entry must be an array containing a 'class' key", $name));
        } elseif (!class_exists($entry[self::CLASS_KEY])) {
            throw new ContainerException(sprintf('%s service class does not exist : %s',
                $name,
                $entry[self::CLASS_KEY]));
        } elseif (isset($entry[self::LOCK_KEY])) {
            throw new ContainerException("%s service contains a circular reference", $name);
        }
        $entry[self::LOCK_KEY] = true;

        $arguments = isset($entry[self::ARGUMENTS_KEY]) ?
            $this->resolveArguments($name, $entry[self::ARGUMENTS_KEY]) :
            [];

        $reflector = new \ReflectionClass($entry['class']);
        $serviceObject = $reflector->newInstanceArgs($arguments);
        if (isset($entry[self::CALL_KEY])) {
            $this->initializeService(
                $serviceObject,
                $name,
                $entry[self::CALL_KEY]
            );
        }
        return $serviceObject;
    }

    /**
     * resolve arguments
     *
     * @param $name
     * @param array $argumentDefinitions
     * @return array
     */
    private function resolveArguments(array $argumentDefinitions)
    {
        $arguments = [];
        foreach ($argumentDefinitions as $argumentDefinition) {
            if ($argumentDefinition instanceof ServiceReference) {
                // use the get method to get service
                $arguments[] = $this->get($argumentDefinition->getName());
            } elseif ($argumentDefinition instanceof ParameterReference) {
                $arguments[] = $this->getParameter($argumentDefinition->getName());
            }
        }
        return $arguments;
    }

    /**
     *init service and call method
     *
     * @param $service
     * @param $name
     * @param array $callDefinitions
     * @throws ContainerException
     */
    private function initializeService($serviceObject, $name, array $callDefinitions)
    {
        foreach ($callDefinitions as $callDefinition) {
            if (!is_array($callDefinition) || !isset($callDefinition['method'])) {
                throw new ContainerException(sprintf("%s service calls must be arrays containing a 'method' key", $name));
            } elseif (!is_callable([$serviceObject, $callDefinition['method']])) {
                throw new ContainerException(sprintf(
                    "% service asks for call to uncallable method: %S",
                    $name,
                    $callDefinition['method']));
            }
            $arguments = isset($callDefinition['arguments']) ?
                $this->resolveArguments($callDefinition['arguments']) :
                [];
            call_user_func_array([
                $serviceObject,
                $callDefinition['method']],
                $arguments
            );
        }
    }

}