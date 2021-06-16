<?php declare(strict_types = 1);

namespace Contributte\Facebook;

use Contributte\Facebook\Exceptions\FacebookLoginException;
use Contributte\Facebook\Exceptions\FacebookTokenException;
use Exception;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\Facebook;

/**
 * Class LoginService
 */
class FacebookLogin
{

	/** @var Facebook */
	private $facebook;

	public function __construct(Facebook $facebook)
	{
		$this->facebook = $facebook;
	}

	/**
	 * Creates Response that redirects person to FB for authorization and back
	 */
	public function getLoginUrl(string $redirectUrl): string
	{
		return $this->facebook->getAuthorizationUrl(['redirect_uri' => $redirectUrl]);
	}

	/**
	 * Gets access token from fb for queried user
	 * @throws FacebookLoginException
	 */
	public function getAccessToken(): AccessToken
	{
		try {
			$accessToken = $this->facebook->getAccessToken('authorization_code', [
				'code' => $_GET['code']
			]);

			if (!isset($accessToken)) {
				throw new FacebookLoginException('Facebook: can\'t get access token.');
			}

			$accessToken = $this->getLongLifeValidatedToken();
		} catch (Exception $e) {
			throw new FacebookLoginException($e->getMessage());
		}

		return $accessToken;
	}

	/**
	 * Get FB user data
	 * @return FacebookUser|null
	 * @throws FacebookTokenException
	 */
	public function getMe(string $code, string $redirectUrl): ?FacebookUser
	{
		try {
			$accessToken = $this->facebook->getAccessToken('authorization_code', [
				'code' => $code,
				'redirect_uri' => $redirectUrl
			]);
			return $this->facebook->getResourceOwner($accessToken);
		} catch (Exception $e) {
			throw new FacebookLoginException($e->getMessage());
		}
	}

	/**
	 * @throws FacebookTokenException
	 */
	private function getLongLifeValidatedToken(): AccessToken
	{
		try {
			$token = $this->facebook->getLongLivedAccessToken($this->facebook->getAccessToken());
		} catch (Exception $e) {
			throw new FacebookTokenException($e->getMessage());
		}

		return $token;
	}

}
