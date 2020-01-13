<?php

namespace Skidaatl\Convirza;

use Illuminate\Support\Facades\Cache;
use Skidaatl\Convirza\Support\ReportCollection;

class Convirza
{
	protected $api;

	protected $config;

	protected $cache;

	const GROUP_LIST_ENDPOINT = '/group/list';

	const CALL_LIST_ENDPOINT = '/call/list';

	public function __construct($config = [], $api = null)
	{
		$this->config = $config;

		if (is_null($api)) {
			$api = new ConvirzaApi($config);
		}

		$this->cache = Cache::store($config['cache']['store']);

		$this->api = $api;
	}

	public function getCalls($parameters = [])
	{
		$parameters['offset'] = $parameters['offset'] ?? 0;

		$calls = $this->api
			->request('GET', self::CALL_LIST_ENDPOINT, $parameters);

		if(!isset($parameters['limit'])) {
			while(count($calls == 100)) {
				$parameters['offset'] += 100;
				$calls = array_merge($calls, $this->getCalls($parameters));
				return $calls;
			}
		}

		return $calls;
	}

	public function getGroups($parameters = [])
	{
		$expires_at = now()->addSeconds($this->config['cache']['duration']);

		if($this->cache->has('convirza_groups')) {
			return $this->cache->get('convirza_groups');
		}

		$parameters['offset'] = $parameters['offset'] ?? 0;

		$groups = collect();

		$response = $this->api
			->request('GET', self::GROUP_LIST_ENDPOINT, $parameters);

		$groups = $groups->merge($response);

		if(!isset($parameters['limit'])) {
			while(count($response) == 100) {
				$parameters['offset'] += 100;
				$response = $this->getGroups($parameters);
				$groups = $groups->merge($response);
			}
		}

		$this->cache->put('convirza_groups', $groups, $expires_at);

		return $groups;
	}

	public function getCall($id, $parameters = [])
	{
		$tokens = [
			'id' => $id
		];

		return $this->api->request('GET', 'v2/call', $tokens, $parameters);
	}

	public function getReport($parameters = [], $write = true)
	{
		$report = $this->api
			->setEndpoint('https://apicfa.convirza.com/v2')
			->request('GET', '/call/groupActivity', $parameters);

		return new ReportCollection($report);
	}
}
