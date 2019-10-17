<?php

namespace Project\loggers;

use Project\Logger;
use Project\managers\FileManager;

/**
 * Class LogFile
 * @package Project\loggers
 */
class LogFile implements Logger
{
    /**
     * @param string $errorType
     * @param string $errorMessage
     * @return void
     */
    public function log(string $errorType, string $errorMessage)
    {
        $exception = unserialize($errorMessage);
        $content = date('Y-m-d H:i:s') . ' | Type: ' . $errorType . ' | File: ' . $exception->getFile() .
            ' | Line: ' . $exception->getLine() . ' | Message: ' . $exception->getMessage() . "\n";
        (new FileManager())->put($this->getRootDir() . DIRECTORY_SEPARATOR . 'error.log', $content, true);
    }

    /**
     * @return string
     */
    private function getRootDir(): string
    {
        $rootDir = $_SERVER['DOCUMENT_ROOT'];

        if (empty($rootDir)) {
            $rootDir = $_SERVER['SCRIPT_FILENAME'];
        }

        return dirname(realpath($rootDir));
    }
}