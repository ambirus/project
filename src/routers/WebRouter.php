<?php

namespace Project\routers;

use Exception;
use Project\App;

/**
 * Class WebRouter.
 */
class WebRouter implements Routing
{
    /**
     * @var string
     */
    private static $moduleName;

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
     * @var string
     */
    private $namespace = 'application\\controllers\\';

    /**
     * @throws Exception
     *
     * @return mixed
     */
    public function execute()
    {
        $route = $this->getShortUrl($_SERVER['REQUEST_URI']);
        $routeParts = explode('/', $route);

        $controllerPos = 1;
        $actionPos = 2;
        $paramsPos = 3;

        if ($routeParts[$controllerPos] === 'modules') {
            $this->namespace = 'application\\modules\\'.$routeParts[$actionPos].'\\controllers\\';
            self::$moduleName = $routeParts[$actionPos];
            $controllerPos = 3;
            $actionPos = 4;
            $paramsPos = 5;
        }

        $rawControllerName = !empty($routeParts[$controllerPos]) ? $routeParts[$controllerPos] : self::$controllerName;
        $rawActionName = !empty($routeParts[$actionPos]) ? $routeParts[$actionPos] : self::$actionName;
        $rawParams = $routeParts[$paramsPos] ?? '';

        /*
         * handling controller
         */
        self::$controllerName = $this->getController($rawControllerName);

        /*
         * handling action
         */
        self::$actionName = $this->getAction($rawActionName);

        /*
         * handling parameters
         */
        self::$actionParams = $this->getParams($rawParams);

        $controllerName = self::$controllerName.'Controller';
        $actionName = 'action'.self::$actionName;
        $namespaceController = $this->namespace.$controllerName;

        if (!class_exists($namespaceController)) {
            throw new Exception(__CLASS__.': '.'No such class &laquo;'.$namespaceController.'&raquo;');
        }

        $controllerInstance = new $namespaceController;

        if (!method_exists($controllerInstance, $actionName)) {
            throw new Exception(__CLASS__.': '.'No such controller action &laquo;'.$actionName.'&raquo;');
        }

        return $controllerInstance->$actionName(self::$actionParams);
    }

    /**
     * @return string
     */
    public static function getCurrentModuleName(): string
    {
        return strtolower(self::$moduleName);
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
     *
     * @throws Exception
     *
     * @return string
     */
    private function getShortUrl(string $url): string
    {
        $shortRoutes = App::getConfig()->get('routes.php');

        foreach ($shortRoutes as $pattern => $route) {
            if (preg_match('/'.$pattern.'/sU', $url)) {
                $urlParts = explode('/', $url);
                array_shift($urlParts);
                $keys = array_keys($urlParts);

                foreach ($keys as $key) {
                    $route = str_replace('{'.$key.'}', $urlParts[$key], $route);
                }

                return $route;
            }
        }

        return $url;
    }

    /**
     * @param string $controllerName
     *
     * @return string
     */
    private function getController(string $controllerName): string
    {
        return ucfirst($controllerName);
    }

    /**
     * @param string $actionName
     *
     * @return string
     */
    private function getAction(string $actionName): string
    {
        return ucfirst($actionName);
    }

    /**
     * @param string $paramsStr
     *
     * @return array
     */
    private function getParams(string $paramsStr): array
    {
        $actionParams = [];

        if (!empty($_GET['page'])) {
            $actionParams['page'] = intval($_GET['page']);
        }

        if (!empty($paramsStr)) {
            $params = explode('&', $paramsStr);
            foreach ($params as $param) {
                $tmp = explode('=', $param);
                $actionParams[$tmp[0]] = $tmp[1] ? str_replace('?page', '', $tmp[1]) : null;
            }
        }

        return $actionParams;
    }
}
