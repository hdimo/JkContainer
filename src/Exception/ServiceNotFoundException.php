<?php 
namespace Jk\Container\Exception;

use Interop\Container\Exception\ContainerException as InteropContainerException;

class ServiceNotFoundException
    extends \Exception 
    implements InteropContainerException
{}