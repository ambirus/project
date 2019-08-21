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

	public function get(): PagerDataValue
	{
		$pagerDataValue = new PagerDataValue();
		
		return $pagerDataValue;
	}
}
