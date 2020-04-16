<?php

namespace Project;

use ReflectionClass;
use ReflectionException;

/**
 * Class Dictionary.
 */
abstract class Dictionary
{
    /**
     * @throws ReflectionException
     *
     * @return array
     */
    public static function get(): array
    {
        $constants = (new ReflectionClass(static::class))->getConstants();

        return array_values($constants);
    }
}
