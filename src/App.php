<?php

namespace Project;

use Project\routers\ConsoleRouter;
use Project\routers\WebRouter;

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

    public function run()
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            $router = new ConsoleRouter();
        } else {
            $router = new WebRouter();
        }

        $router->execute();
    }
}
