<?php

namespace Project\values;

/**
 * Class PagerDataValue
 * @package Project\values
 */
class PagerDataValue
{
    /**
     * @var int
     */
    private $startValue = 1;
    /**
     * @var int
     */
    private $endValue;
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
     * @var int
     */
    private $countInARow = 6;
    /**
     * @var bool
     */
    private $needSeparator = false;

    /**
     * @return int
     */
    public function getStartValue(): int
    {
        return $this->startValue;
    }

    /**
     * @return int
     */
    public function getEndValue(): int
    {
        return $this->endValue;
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
     * @return int
     */
    public function getCountInARow(): int
    {
        return $this->countInARow;
    }
    /**
     * @return bool
     */
    public function getNeedSeparator(): bool
    {
        return $this->needSeparator;
    }
}
