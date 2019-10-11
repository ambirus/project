<?php

namespace Project\managers;

use Project\App;
use Project\Logger;
use Exception;

/**
 * Class LoggerManager
 * @package Project\managers
 */
class LoggerManager
{
    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        $loggerClass = App::getConfig()->get('system.logger')[0];

        if (!class_exists($loggerClass)) {
            throw new Exception($loggerClass . ' not found');
        }

        return new $loggerClass;
    }
}