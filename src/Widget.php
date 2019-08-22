<?php

namespace Project;

use Exception;
use ReflectionClass;
use ReflectionException;

/**
 * Class Widget
 * @package Project
 */
abstract class Widget
{
    /**
     * @var string
     */
    protected $widgetViewPath;

    /**
     * Widget constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->widgetViewPath = 'widgets' . DIRECTORY_SEPARATOR . $this->getWidgetName() . DIRECTORY_SEPARATOR . 'views';
    }

    /**
     * @param array $params
     * @return mixed
     */
    public abstract function run(array $params = []);

    /**
     * @return string
     * @throws ReflectionException
     */
    private function getWidgetName(): string
    {
        $shortName = strtolower((new ReflectionClass($this))->getShortName());
        return str_replace('widget', '', $shortName);
    }
}
