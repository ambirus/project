<?php

namespace Project;

use Project\routers\{
    ConsoleRouter, WebRouter
};

class App
{
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