<?php

namespace Project\routers;

use Exception;

class ConsoleRouter implements Routing
{
    /**
     * @throws Exception
     */
    public function execute()
    {
        $commandName = 'Import';
        $actionName = 'Index';

        $params = [];
        $actionParams = [];

        if ($_SERVER['argc'] > 2) {
            for ($i = 2; $i < $_SERVER['argc']; $i++) {
                $params[] = $_SERVER['argv'][$i];
            }
        }

        if (!isset($_SERVER['argv'][1]))
            return;

        $routes = explode('/', $_SERVER['argv'][1]);

        if (!empty($routes[0])) {
            $commandName = ucfirst($routes[0]);
        }

        if (!empty($routes[1])) {
            $actionName = ucfirst($routes[1]);
        }

        if (sizeof($params) > 0) {
            foreach ($params as $param) {
                $tmp = explode('=', $param);
                $actionParams[$tmp[0]] = isset($tmp[1]) ? $tmp[1] : null;
            }
        }

        $commandName = $commandName . 'Command';
        $actionName = 'action' . $actionName;
        $namespaceController = 'application\\commands\\' . $commandName;

        if (!class_exists($namespaceController)) {
            throw new Exception(__CLASS__ . ': ' . 'No such class ' . $namespaceController . "\n");
        }

        $controller = new $namespaceController;

        $action = $actionName;

        if (method_exists($controller, $action)) {

            $controller->$action($actionParams);

        } else {
            throw new Exception(__CLASS__ . ': ' . 'No such controller action ' . $action . "\n");
        }
    }
}
