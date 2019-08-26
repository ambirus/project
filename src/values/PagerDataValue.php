<?php

namespace Project\values;

use ReflectionClass;
use ReflectionException;

/**
 * Class PagerDataValue
 * @package Project\values
 */
class PagerDataValue
{
    /**
     * @var bool
     */
    private $lt;
    /**
     * @var int
     */
    private $startValue;
    /**
     * @var bool
     */
    private $needLeftDots;
    /**
     * @var array
     */
    private $body;
    /**
     * @var bool
     */
    private $needRightDots;
    /**
     * @var int
     */
    private $endValue;
    /**
     * @var bool
     */
    private $rt;
    /**
     * @var int
     */
    private $currentValue;
    /**
     * @var int
     */
    private $leftValue;
    /**
     * @var int
     */
    private $rightValue;

    /**
     * @return bool
     */
    public function getLt(): bool
    {
        return $this->lt;
    }

    /**
     * @return int
     */
    public function getStartValue(): int
    {
        return $this->startValue;
    }

    /**
     * @return bool
     */
    public function getLeftDots(): bool
    {
        return $this->needLeftDots;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @return bool
     */
    public function getRightDots(): bool
    {
        return $this->needRightDots;
    }

    /**
     * @return int
     */
    public function getEndValue(): int
    {
        return $this->endValue;
    }

    /**
     * @return bool
     */
    public function getRt(): bool
    {
        return $this->rt;
    }

    /**
     * @return int
     */
    public function getCurrentValue(): int
    {
        return $this->currentValue;
    }

    /**
     * @return int
     */
    public function getLeftValue(): int
    {
        return $this->leftValue;
    }

    /**
     * @return int
     */
    public function getRightValue(): int
    {
        return $this->rightValue;
    }

    /**
     * @param array $data
     * @return PagerDataValue
     * @throws ReflectionException
     */
    public function load(array $data): PagerDataValue
    {
        $props = (new ReflectionClass($this))->getProperties();

        foreach ($props as $prop) {
            $propName = $prop->getName();

            if (isset($data[$propName])) {
                $this->$propName = $data[$propName];
            }
        }

        return $this;
    }
}
