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
     * @param string $errorTitle
     * @param string $errorMessage
     */
    public function log(string $errorType, string $errorTitle, string $errorMessage)
    {
        $this->logInstance
            ->create([
                'type' => $errorType,
                'title' => $errorTitle,
                'message' => str_replace("\n", '', $errorMessage),
            ])
            ->execute();
    }
}
