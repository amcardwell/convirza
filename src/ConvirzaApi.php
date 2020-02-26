<?php

namespace Skidaatl\Convirza;

use Skidaatl\Convirza\Exception\ConvirzaApiException;
use Skidaatl\Convirza\Exception\ConvirzaBadRequestException;
use Skidaatl\Convirza\Exception\ConvirzaException;
use Skidaatl\Convirza\Http\Client;
use Skidaatl\Convirza\Http\ConvirzaResponse;

class ConvirzaApi
{
	/**
	 * The HTTP client.
	 *
	 * @var Client $client
	 */
	protected $client;

	/**
	 * The config object.
	 *
	 * @var array $config
	 */
	protected $config;

	/**
	 * The Authentication class
	 * @var ConvirzaAuth $auth
	 */
	protected $auth;

	/**
	 * The REST API Endpoint
	 * @var string $endpoint
	 */
	protected $endpoint = 'https://apicfa.convirza.com/v2';

	/**
	 * The API response code
	 * @var string $responseCode
	 */
	protected $responseCode;

	/**
	 * ConvirzaApi Constructor
	 * @param array $config
	 */
	public function __construct($config = [])
	{
		$this->config = $config;

		$this->client = new Client([
			'base_uri' => $this->endpoint,
			'debug' => $config['debug']
		]);

		$this->auth = new ConvirzaAuth;

		if($this->auth->isEmpty() || $this->auth->isExpired()) {
			$this->fetchApiKey();
		}
	}

	public function getAuth()
	{
		return $this->auth;
	}

	public function fetchApiKey()
	{
		$response = $this->client->handleRequest('POST', '/oauth/token', null, [
			'grant_type' => 'password',
			'client_id' => 'system',
			'client_secret' => 'f558ba166258089b2ef322c340554c',
			'username' => $this->config['username'],
			'password' => $this->config['password']
		]);

		$this->auth->setAccessToken($response['access_token'], $response['expires_in']);
	}

	public function setEndpoint(string $endpoint)
	{
		$this->endpoint = $endpoint;

		return $this;
	}

	/**
	 * Makes a request to the Convirza API.
	 *
	 * @param string $method
	 *   The REST method to use when making the request.
	 * @param string $path
	 *   The API path to request.
	 * @param array $tokens
	 *   Associative array of tokens and values to replace in the path.
	 * @param array $parameters
	 *   Associative array of parameters to send in the request body.
	 *
	 * @return ConvirzaResponse
	 *
	 * @throws ConvirzaApiException
	 */
	public function request($method, $path, $tokens = NULL, $parameters = NULL)
	{
		$options = [
			'headers' => [
				'Authorization' => 'bearer ' . $this->auth->getToken()
			]
		];

		$response = $this->client->handleRequest($method, $this->endpoint . $path, $options, $tokens, $parameters);

		return new ConvirzaResponse($response);
	}
}
