<?php

namespace Skidaatl\Convirza\Support;

use Illuminate\Support\Collection;

class ReportCollection extends Collection
{
	public function __construct($items)
	{
		$this->items = $items;
	}
}
