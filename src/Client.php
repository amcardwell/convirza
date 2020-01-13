<?php

namespace Skidaatl\Convirza;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Skidaatl\Convirza\Exception\ConvirzaApiException;

class Client
{
	/**
	 * The GuzzleHttp client.
	 *
	 * @var Client $client
	 */
	private $client;

	/**
	 * Client constructor.
	 *
	 * @param array $config
	 * Guzzle HTTP configuration options.
	 */
	public function __construct($config = []) {
		$this->client = new GuzzleClient($config);
	}

	/**
	 * @inheritdoc
	 */
	public function handleRequest($method, $uri = '', $options = [], $tokens = [], $parameters = [])
	{
		if (!empty($tokens)) {
			if ($method == 'GET') {
	        // Send parameters as query string parameters.
				$options['query'] = $tokens;
			}
			else {
	        // Send parameters as JSON in request body.
				$options['json'] = (object) $tokens;
			}
		}

		try {
			$response = $this->client->request($method, $uri, $options);
			$data = json_decode($response->getBody(), true);

			return $data;
		}
		catch (RequestException $e) {
			$response = $e->getResponse();
			if (!empty($response)) {
				$message = $e->getResponse()->getBody();
			}
			else {
				$message = $e->getMessage();
			}

			throw new ConvirzaApiException($message, $e->getCode(), $e);
		}
	}
}
