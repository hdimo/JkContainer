<?php
/**
 * Created by PhpStorm.
 * User: khaled
 * Date: 8/28/17
 * Time: 2:13 PM
 */

namespace Jk\Container;


interface IParameterAware
{
    public function getParameter($name);
}