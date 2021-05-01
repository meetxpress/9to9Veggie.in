<?php

namespace GoDaddy\WordPress\MWC\Core\Tests;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class TestHelpers
{
    /**
     * Allow for calling protected and private methods on a class.
     *
     * @param $class
     * @param string $name
     *
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    public static function getStaticHiddenMethod($class, string $name)
    {
        $class = new ReflectionClass($class);
        $method = $class->getMethod($name);

        $method->setAccessible(true);

        return $method;
    }
}
