<?php

namespace Skidaatl\Convirza\Support;

use Convirza;
use Illuminate\Support\Collection;

class GroupItem extends Collection
{
	public function __construct($items = [])
	{
		$this->items = $items;
	}

	public function campaigns()
	{
		return Convirza::fetchCampaigns(['filter' => 'group_id='.$this->items['group_id']]);
	}
}
