<?php

namespace Project\dictionaries\db;

use Project\Dictionary;
use PDO;

/**
 * Class ParamsDictionary
 * @package Project\dictionaries\db
 */
class ParamsDictionary extends Dictionary
{
    const PARAM_INT = PDO::PARAM_INT;
    const PARAM_STR = PDO::PARAM_STR;
    const PARAM_BOOL = PDO::PARAM_BOOL;
}
