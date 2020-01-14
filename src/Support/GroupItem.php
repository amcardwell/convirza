<?php

namespace Skidaatl\Convirza\Support;

use Illuminate\Support\Collection;

class GroupItem extends Collection
{
	public function __construct($items = [])
	{
		$this->items = $items;
	}
}
