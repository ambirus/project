<?php

namespace Project;

use Project\values\PagerDataValue;

/**
 * Class Pager.
 */
class Pager
{
    const PAGER_LIMIT = 20;

    /**
     * @var int
     */
    private $totalCount;

    /**
     * @var int
     */
    private $currValue;

    /**
     * Pager constructor.
     *
     * @param int $totalCount
     * @param int $currValue
     */
    public function __construct(int $totalCount, int $currValue = 1)
    {
        $this->totalCount = $totalCount;
        $this->currValue = $currValue > 0 ? $currValue : 1;
    }

    /**
     * @return PagerDataValue
     */
    public function get(): PagerDataValue
    {
        if ($this->totalCount <= self::PAGER_LIMIT) {
            return new PagerDataValue();
        }

        $data = $this->getPreparedData();

        return new PagerDataValue($data);
    }

    /**
     * @return array
     */
    private function getPreparedData(): array
    {
        $endValue = (int) ceil($this->totalCount / self::PAGER_LIMIT);
        $body = [];
        $leftBody = $this->currValue - 3;
        $rightBody = $this->currValue + 3;

        if ($leftBody >= 1 || $leftBody < 1) {
            for ($i = $this->currValue; $i >= $leftBody; $i--) {
                if ($i > 1 && $i < $endValue) {
                    $body[] = $i;
                }
            }
        }

        if ($rightBody <= $endValue || $rightBody > $endValue) {
            for ($i = $this->currValue; $i <= $rightBody; $i++) {
                if ($i > 1 && $i < $endValue) {
                    $body[] = $i;
                }
            }
        }

        sort($body);

        return [
            'lt' => $this->currValue > 1 ? true : false,
            'rt' => $this->currValue < $endValue ? true : false,
            'startValue' => 1,
            'endValue' => $endValue,
            'leftValue' => $this->currValue - 1,
            'currentValue' => $this->currValue,
            'rightValue' => $this->currValue + 1,
            'needLeftDots' => $this->currValue + 4 >= 10 ? true : false,
            'body' => array_unique($body),
            'needRightDots' => $endValue - $this->currValue < 5 ? false : true,
        ];
    }
}
