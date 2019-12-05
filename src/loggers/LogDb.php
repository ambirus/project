<?php

namespace Project\loggers;

use Project\db\Table;
use Project\Logger;

/**
 * Class LogDb
 * @package Project\loggers
 */
class LogDb implements Logger
{
    /**
     * @var Log
     */
    private $logInstance;

    public function __construct()
    {
        $this->logInstance = new Log();
    }

    /**
     * @param string $errorType
     * @param string $errorMessage
     */
    public function log(string $errorType, string $errorMessage)
    {
        $this->logInstance
            ->create([
                'type' => $errorType,
                'message' => str_replace("\n", '', $errorMessage)
            ])
            ->execute();
    }
}

/**
 * Class Log
 * @package Project\loggers
 */
class Log extends Table
{
    /**
     * @var string
     */
    protected $name = 'log';

}