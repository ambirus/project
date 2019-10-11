<?php

namespace Project\loggers;

use Project\Logger;

/**
 * Class LogDb
 * @package Project\loggers
 */
class LogDb implements Logger
{
    /**
     * @param string $errorType
     * @param string $errorMessage
     */
    public function log(string $errorType, string $errorMessage)
    {
        // TODO: Implement log() method.
    }
}