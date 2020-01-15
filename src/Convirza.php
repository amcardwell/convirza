<?php

namespace Skidaatl\Convirza;

use Illuminate\Support\Facades\Cache;

class Convirza
{
	protected $api;

	protected $config;

	protected $cache;

	const GROUP_ENDPOINT = '/group';

	const GROUP_LIST_ENDPOINT = '/group/list';

	const CALL_ENDPOINT = '/call';

	const CALL_LIST_ENDPOINT = '/call/list';

	const CALL_GROUP_ACTIVITY_ENDPOINT = '/call/groupActivity';

	const CAMPAIGN_ENDPOINT = '/campaign';

	const CAMPAIGN_LIST_ENDPOINT = '/campaign/list';

	public function __construct($config = [], $api = null)
	{
		$this->config = $config;

		if (is_null($api)) {
			$api = new ConvirzaApi($config);
		}

		$this->cache = Cache::store($config['cache']['store']);

		$this->api = $api;
	}

	public function fetchCall($id)
	{
		return new Support\CallItem($this->makeCachedRequest('GET', self::CALL_ENDPOINT, ['id' => $id]));
	}

	public function fetchCalls(array $parameters = [])
	{
		$response = $this->makeCachedRequest('GET', self::CALL_LIST_ENDPOINT, $parameters);

		return (new Support\CallCollection($response))->mapInto(Support\CallItem::class);
	}

	public function fetchGroup($id)
	{
		return new Support\GroupItem($this->makeCachedRequest('GET', self::GROUP_ENDPOINT, ['id' => $id]));
	}

	public function fetchGroups(array $parameters = [])
	{
		$response = $this->makeCachedRequest('GET', self::GROUP_LIST_ENDPOINT, $parameters);

		return (new Support\GroupCollection($response))->mapInto(Support\GroupItem::class);
	}

	public function fetchCampaign($id)
	{
		return new Support\CampaignItem($this->makeCachedRequest('GET', self::CAMPAIGN_ENDPOINT, ['id' => $id]));
	}

	public function fetchCampaigns(array $parameters = [])
	{
		$response = $this->makeCachedRequest('GET', self::CAMPAIGN_LIST_ENDPOINT, $parameters);

		return (new Support\CampaignCollection($response))->mapInto(Support\CampaignItem::class);
	}

	public function fetchCallGroupActivity(array $parameters = [])
	{
		$response = $this->makeCachedRequest('GET', self::CALL_GROUP_ACTIVITY_ENDPOINT, $parameters);

		return (new Support\CallGroupActivityCollection($response))->mapInto(Support\CallGroupActivityItem::class);
	}

	private function makeCachedRequest($method, $url, array $parameters = [])
	{
		$expires_at = now()->addSeconds($this->config['cache']['duration']);

		$cacheKey = 'convirza_'.md5($method.$url.json_encode($parameters));

		$args = func_get_args();

		return $this->cache->remember($cacheKey, $expires_at, function() use ($args) {
			return $this->makeRequest(...$args);
		});
	}

	private function makeRequest($method, $url, array $parameters = [])
	{
		$parameters['offset'] = $parameters['offset'] ?? 0;

		$response = $this->api->request($method, $url, $parameters);

		$data = $response->data;

		if(count($data) === 100) {
			while(count($data) === 100) {
				if(isset($parameters['limit'])) {
					$parameters['limit'] -= 100;
				}
				$parameters['offset'] += 100;
				$response = $this->api->request($method, $url, $parameters);
				$data = array_merge($data, $response->data);
			}
		}

		return $data;
	}
}
