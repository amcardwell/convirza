<?php

namespace Skidaatl\Convirza;

class ConvirzaAuth
{
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
	 * @param string $accessToken
	 * @param int    $expiresAt
	 */
	public function __construct($accessToken = null, $expiresAt = 0)
	{
		if(!is_null($accessToken)) {

			$this->token = $accessToken;

			if ($expiresAt) {
				$this->setExpiresAtFromTimeStamp($expiresAt);
			}

		} else {

			$this->fetchAccessTokenFromDatabase();

		}
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
