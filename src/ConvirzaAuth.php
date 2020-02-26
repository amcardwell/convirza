<?php

namespace Skidaatl\Convirza;

use Skidaatl\Convirza\Http\Client;

class ConvirzaAuth
{
	/**
	 * The http client
	 *
	 * @var string
	 */
	protected $client;

	/**
	 * The access token value.
	 *
	 * @var string
	 */
	protected $token = '';

	/**
	 * Date when token expires.
	 *
	 * @var \DateTime|null
	 */
	protected $expiresAt;

	/**
	 * Create a new access token entity.
	 *
	 * @param Skidaatl\Convirza\Http\Client $client
	 * @param string $accessToken
	 * @param int    $expiresAt
	 */
	public function __construct(Client $client, $accessToken = null, $expiresAt = 0)
	{
		$this->client = $client;

		if(!is_null($accessToken)) {

			$this->token = $accessToken;

			if ($expiresAt) {
				$this->setExpiresAtFromTimeStamp($expiresAt);
			}

		} else {

			$this->fetchAccessTokenFromDatabase();

		}

		if($this->isEmpty() || $this->isExpired()) {
			$this->fetchApiKey();
		}
	}

	public function fetchApiKey()
	{
		$response = $this->client->handleRequest('POST', '/oauth/token', null, [
			'grant_type' => 'password',
			'client_id' => 'system',
			'client_secret' => 'f558ba166258089b2ef322c340554c',
			'username' => config('convirza.username'),
			'password' => config('convirza.password')
		]);

		$this->setAccessToken($response['access_token'], $response['expires_in']);
	}

	/**
	 * Fetch access token from the database
	 *
	 * @return array
	 */
	public function fetchAccessTokenFromDatabase()
	{
		$this->token = ConvirzaConfig::where('key', 'api_key')->value('value');

		$this->setExpiresAtFromTimeStamp(
			ConvirzaConfig::where('key', 'api_key_expires')->value('value')
		);
	}

	/**
	 * Dynamically set the access token and expires
	 *
	 * @return self
	 */
	public function setAccessToken($token, $expires = 0)
	{
		$this->token = $token;
		$this->setExpiresAtFromTimeStamp(
			now()->addSeconds($expires)->toDateTimeString()
		);

		ConvirzaConfig::updateOrCreate([
			'key' => 'api_key'
		], ['value' => $token]);

		ConvirzaConfig::updateOrCreate([
			'key' => 'api_key_expires'
		], ['value' => now()->addSeconds($expires)->toDateTimeString()]);
	}

	/**
	 * Getter for expiresAt.
	 *
	 * @return \DateTime|null
	 */
	public function getExpiresAt()
	{
		return $this->expiresAt;
	}

	/**
 	 * Checks the expiration of the access token.
	 *
	 * @return boolean|null
	 */
	public function isExpired()
	{
		if ($this->getExpiresAt() instanceof \DateTime) {
			return $this->getExpiresAt()->getTimestamp() < time();
		}

		return null;
	}

	/**
	 * Returns the access token as a string.
	 *
	 * @return string
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * Returns the access token as a string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getToken();
	}

	/**
 	 * Setter for expires_at.
	 *
	 * @param int $timestamp
	 */
	protected function setExpiresAtFromTimeStamp($timestamp)
	{
		if(!$timestamp) {
			return null;
		}

		$this->expiresAt = \Carbon\Carbon::parse($timestamp);
	}

	/**
 	 * Determines if the class is empty
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		return (bool) !$this->token;
	}
}
