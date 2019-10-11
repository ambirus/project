<?php

namespace Project\dictionaries\logger;

use Project\Dictionary;

/**
 * Class ErrorTypesDictionary
 * @package Project\dictionaries\logger
 */
class ErrorTypesDictionary extends Dictionary
{
    const DB = 'db';
    const NOT_FOUND = 'not_found';
    const REGULAR = 'regular';
    const THROWABLE = 'throwable';
}
