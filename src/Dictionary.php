<?php

namespace Project;

use ReflectionClass;
use ReflectionException;

abstract class Dictionary
{
    /**
     * @return array
     * @throws ReflectionException
     */
    public static function get()
    {
        $constants = (new ReflectionClass(static::class))->getConstants();
        return array_values($constants);
    }
}