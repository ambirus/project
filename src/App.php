<?php

namespace Project;

use Project\routers\ConsoleRouter;
use Project\routers\Routing;
use Project\routers\WebRouter;

/**
 * Class App
 * @package Project
 */
class App
{
    /**
     * @var Config
     */
    private static $config;

    /**
     * App constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        self::$config = $config;
    }

    /**
     * @return Config
     */
    public static function getConfig(): Config
    {
        return self::$config;
    }

    /**
     * @return mixed
     */
    public function run()
    {
        return $this->getRouter()->execute();
    }

    /**
     * @return Routing
     */
    private function getRouter(): Routing
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            return new ConsoleRouter();
        }

        return new WebRouter();
    }
}
