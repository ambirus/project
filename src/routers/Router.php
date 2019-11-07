<?php

namespace Project\routers;

use Project\App;

/**
 * Class Router
 * @package Project\routers
 */
abstract class Router implements Routing
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $middlewares = [];

    /**
     * Router constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->config = App::getConfig()->get('middlewares.php');
        $this->loadMiddlewares();
    }

    /**
     * @return Router
     */
    private function loadMiddlewares(): Router
    {
        if (count($this->config) > 0) {
            $this->middlewares = $this->config;
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}