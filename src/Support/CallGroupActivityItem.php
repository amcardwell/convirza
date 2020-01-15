<?php

namespace Skidaatl\Convirza\Support;

use Convirza;
use Illuminate\Support\Collection;

class CallGroupActivityItem extends Collection
{
	public function __construct($items = [])
	{
		$this->items = $items;
	}
}
