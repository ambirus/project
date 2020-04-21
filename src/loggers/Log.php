<?php

namespace Project\loggers;

use Project\db\Table;

/**
 * Class Log.
 */
class Log extends Table
{
    /**
     * @var string
     */
    protected $name = 'log';

    /**
     * @var bool
     */
    protected $softDelete = true;
}
