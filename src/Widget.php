<?php

namespace Project;

use Exception;
use ReflectionClass;
use ReflectionException;

/**
 * Class Widget.
 */
abstract class Widget
{
    /**
     * @var string
     */
    protected $widgetViewPath;

    /**
     * Widget constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->widgetViewPath = 'widgets'.DIRECTORY_SEPARATOR.$this->getWidgetName().DIRECTORY_SEPARATOR.'views';
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    abstract public function run(array $params = []);

    /**
     * @throws ReflectionException
     *
     * @return string
     */
    private function getWidgetName(): string
    {
        $shortName = strtolower((new ReflectionClass($this))->getShortName());

        return str_replace('widget', '', $shortName);
    }
}
