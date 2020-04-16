<?php

namespace Project\managers;

use Exception;
use Project\App;
use Project\Logger;

/**
 * Class LoggerManager.
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
            throw new Exception($loggerClass.' not found');
        }

        return new $loggerClass;
    }
}
