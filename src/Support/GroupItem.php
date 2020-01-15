<?php

namespace Skidaatl\Convirza\Support;

use Convirza;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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

	public function callGroupActivity(array $params = [])
	{
		if(isset($params['filter'])) {
			$filters = explode(',', $params['filter']);
			foreach($filters as $k => $filter) {
				if(Str::contains($filter, 'group_id')) {
					unset($filters[$k]);
				}
			}
			$filters[] = 'group_id='.$this->items['group_id'];
			$params['filter'] = implode(',', $filters);
		} else {
			$params['filter'] = 'group_id='.$this->items['group_id'];
		}

		return Convirza::fetchCallGroupActivity($params);
	}
}
