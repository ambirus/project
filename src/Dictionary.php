<?php

namespace Project;

use ReflectionClass;
use ReflectionException;

/**
 * Class Dictionary
 * @package Project
 */
abstract class Dictionary
{
    /**
     * @return array
     * @throws ReflectionException
     */
    public static function get(): array 
    {
        $constants = (new ReflectionClass(static::class))->getConstants();
        return array_values($constants);
    }
}
