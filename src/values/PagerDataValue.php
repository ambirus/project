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
    private $lt = false;
    /**
     * @var int
     */
    private $startValue;
    /**
     * @var bool
     */
    private $needLeftDots = false;
    /**
     * @var array
     */
    private $body = [];
    /**
     * @var bool
     */
    private $needRightDots = false;
    /**
     * @var int
     */
    private $endValue = 0;
    /**
     * @var bool
     */
    private $rt = false;
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
     * @var bool
     */
    private $isEmpty = true;

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
     * @return bool
     */
    public function getIsEmpty(): bool
    {
        return $this->isEmpty;
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
        $this->isEmpty = false;
        
        return $this;
    }
}
