<?php

namespace Project\loggers;

use Project\Logger;

/**
 * Class LogDb.
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
                'message' => str_replace("\n", '', $errorMessage),
            ])
            ->execute();
    }
}
