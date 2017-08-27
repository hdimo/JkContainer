<?php 
namespace Jk\Container\Exception;


use Interop\Container\Exception\ContainerException as InteropContainerException;

class ParameterNotFoundException 
    extends \Exception 
    implements InteropNotFoundException
{}