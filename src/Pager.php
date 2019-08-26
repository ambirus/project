<?php

namespace Project;

use Project\values\PagerDataValue;

class Pager
{
	private $totalCount;
	private $currValue;

	public function __construct(int $totalCount, int $currValue = 1)
	{
		$this->totalCount = $totalCount;
		$this->currValue = $currValue;
	}

    /**
     * @return PagerDataValue
     */
	public function get(): PagerDataValue
	{
        $endValue = (int) ceil($this->totalCount / 20);
        $body = [];
        if ($endValue > 2) {
            for ($i = 2; $i < $endValue; $i++) {
                $body[] = $i;
            }
        }
		$data = [
		    'lt' => $this->currValue > 1 ? true : false,
            'startValue' => 1,
            'needLeftDots' => $this->currValue > 6 ? true : false,
            'body' => $body,
            'needRightDots' => $end - $this->currValue < 6 ? false : true,
		    'endValue' => $endValue,
            'rt' => $this->currValue < $endValue ? true : false,
            'currentValue' => $this->currValue,
            'leftValue' => $this->currValue - 1,
            'rightValue' => $this->currValue + 1,

        ];

		return (new PagerDataValue())->load($data);
	}
}
