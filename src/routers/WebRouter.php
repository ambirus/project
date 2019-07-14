<?php

namespace Project\routers;

use Exception;
use Project\App;

class WebRouter implements Routing
{
    /**
     * @var string
     */
    private static $controllerName = 'Index';
    /**
     * @var string
     */
    private static $actionName = 'Index';
    private static $actionParams;
    private static $controller;

    /**
     * @throws Exception
     */
    public function execute()
    {
        $actionParams = [];
        $route = $this->getShortUrl($_SERVER['REQUEST_URI']);
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
        $namespaceController = 'application\\controllers\\' . $controllerName;

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

    /**
     * @return string
     */
    public static function getCurrentControllerName(): string
    {
        return strtolower(self::$controllerName);
    }

    /**
     * @return string
     */
    public static function getCurrentActionName(): string
    {
        return strtolower(self::$actionName);
    }

    /**
     * @return mixed
     */
    public static function getCurrentController()
    {
        return self::$controller;
    }

    /**
     * @return mixed
     */
    public static function getActionParams()
    {
        return self::$actionParams;
    }

    /**
     * @param string $url
     * @return string
     * @throws Exception
     */
    private function getShortUrl(string $url): string
    {
        $shortRoutes = App::getConfig()->get('routes');

        foreach ($shortRoutes as $pattern => $route) {
            if (preg_match("/" . $pattern . "/sU", $url)) {
                $urlParts = explode('/', $url);
                array_shift($urlParts);
                $keys = array_keys($urlParts);

                foreach ($keys as $key) {
                    $route = str_replace('{' . $key . '}', $urlParts[$key], $route);
                }

                return $route;
            }
        }

        return $url;
    }
}
