<?php

namespace Skidaatl\Convirza\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class GroupCollection extends Collection
{
	public function __construct($items = [])
	{
		$this->items = $items;
	}
}
