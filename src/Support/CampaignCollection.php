<?php

namespace Skidaatl\Convirza\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CampaignCollection extends Collection
{
	public function __construct($items = [])
	{
		$this->items = $items;
	}
}
