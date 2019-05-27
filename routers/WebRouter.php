<?php

namespace lib\routers;

use Exception;
use lib\Config;

class WebRouter implements Routing
{
    private static $controllerName = 'Index';
    private static $actionName = 'Index';
    private static $actionParams;
    private static $controller;

    /**
     * @throws Exception
     */
    public function execute()
    {
        $actionParams = [];
        $shortRoutes = Config::get('routes');

        $route = isset($shortRoutes['routes'][$_SERVER['REQUEST_URI']]) ? $shortRoutes['routes'][$_SERVER['REQUEST_URI']] : $_SERVER['REQUEST_URI'];

        $routeParts = explode('/', $route);

        if (!empty($routeParts[1])) {
            self::$controllerName = ucfirst($routeParts[1]);
        }

        if (!empty($routeParts[2])) {

            if (strpos($routeParts[2], '=')) {
                $routes[3] = $routeParts[2];
                $routes[4] = 'index';
            }

            self::$actionName = ucfirst($routeParts[2]);
        }

        if (!empty($routeParts[3])) {
            $params = explode('&', $routeParts[3]);

            foreach ($params as $param) {
                $tmp = explode('=', $param);
                $actionParams[$tmp[0]] = isset($tmp[1]) ? $tmp[1] : null;
            }
            self::$actionParams = $actionParams;
        }

        $controllerName = self::$controllerName . 'Controller';
        $actionName = 'action' . self::$actionName;
        $namespaceController = 'src\\controllers\\' . $controllerName;

        if (!class_exists($namespaceController)) {
            throw new Exception(__CLASS__ . ': ' . 'No such class &laquo;' . $namespaceController . '&raquo;');
        }

        $controller = new $namespaceController;

        $action = $actionName;

        if (method_exists($controller, $action)) {

            $controller->$action($actionParams);

        } else {
            throw new Exception(__CLASS__ . ': ' . 'No such controller action &laquo;' . $action . '&raquo;');
        }
    }

    public static function getCurrentControllerName()
    {
        return strtolower(self::$controllerName);
    }

    public static function getCurrentActionName()
    {
        return strtolower(self::$actionName);
    }

    public static function getCurrentController()
    {
        return self::$controller;
    }

    public static function getActionParams()
    {
        return self::$actionParams;
    }
}
