<?php

namespace Skidaatl\Convirza;

class Convirza
{
	protected $api;

	protected $config;

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
		$data = [];

		$parameters['offset'] = 0;

		$i = 0;

		$response = $this->api->request('GET', 'v2/call/list', $parameters);

		while(count($response) === 100) {
			if($i === 5) { break; }
			array_push($data, $response);
			$parameters['offset'] += 100;
			$response = $this->api->request('GET', 'v2/call/list', $parameters);
			$i++;
		}

		return array_flatten($data, 1);
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
			->setEndpoint('https://api.convirza.com/v1/report')
			->request('GET', '/groupActivities', $parameters);

		return $report[0];
	}
}
