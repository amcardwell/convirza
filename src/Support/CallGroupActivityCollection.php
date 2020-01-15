<?php

namespace Skidaatl\Convirza\Support;

use Illuminate\Support\Collection;

class CallGroupActivityCollection extends Collection
{
	public function __construct($items)
	{
		$this->items = $items;
	}
}
