<?php

namespace Skidaatl\Convirza;

use Skidaatl\Convirza\Support\ReportCollection;

class Convirza
{
	protected $api;

	protected $config;

	const GROUP_LIST_ENDPOINT = '/group/list';

	const CALL_LIST_ENDPOINT = '/call/list';

	public function __construct($config = [], $api = null)
	{
		$this->config = $config;

		if (is_null($api)) {
			$api = new ConvirzaApi($config);
		}

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
		$parameters['offset'] = $parameters['offset'] ?? 0;

		$groups = $this->api
			->request('GET', self::GROUP_LIST_ENDPOINT, $parameters);

		if(!isset($parameters['limit'])) {
			while(count($groups) == 100) {
				$parameters['offset'] += 100;
				$groups = array_merge($groups, $this->getGroups($parameters));
				return $groups;
			}
		}

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
