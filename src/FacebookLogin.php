<?php declare(strict_types = 1);

namespace Contributte\Facebook;

use Contributte\Facebook\Exceptions\FacebookLoginException;
use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphUser;
use Nette\Http\Session;

/**
 * Class LoginService
 *
 * @author Filip Suska <filipsuska@gmail.com>
 */
class FacebookLogin
{

	/** @var Facebook */
	private $facebook;

	/** @var Session */
	private $session;

	/**
	 * @param Facebook $facebook
	 * @param Session $session
	 */
	public function __construct(Facebook $facebook, Session $session)
	{
		$this->facebook = $facebook;
		$this->session = $session;
	}

	/**
	 * Creates Response that redirects person to FB for authorization and back
	 *
	 * @param string $redirectUrl
	 * @param string[] $permissions
	 * @return string
	 */
	public function getLoginurl(string $redirectUrl, array $permissions = ['public_profile']): string
	{
		// FB requires session started
		$this->session->start();

		// Create redirect URL with econea return URL
		$helper = $this->facebook->getRedirectLoginHelper();
		$url = $helper->getLoginUrl($redirectUrl, $permissions);

		return $url;
	}

	/**
	 * Gets access token from fb for queried user
	 *
	 * @return AccessToken
	 * @throws FacebookLoginException
	 */
	public function getAccessToken(): AccessToken
	{
		// FB requires session started
		$this->session->start();

		$helper = $this->facebook->getRedirectLoginHelper();

		try {
			// Get accessToken from $_GET
			$accessToken = $helper->getAccessToken();

			// Failed to get accessToken
			if (!isset($accessToken)) {
				if ($helper->getError()) {
					throw new FacebookLoginException($helper->getError());
				} else {
					throw new FacebookLoginException('Facebook: Bad request.');
				}
			}

			$accessToken = $this->getLongLifeValidatedToken($accessToken);
		} catch (FacebookResponseException | FacebookSDKException $e) {
			throw new FacebookLoginException($e->getMessage());
		}

		return $accessToken;
	}

	/**
	 * @return AccessToken
	 * @throws FacebookLoginException
	 */
	public function getAccessTokenFromCookie(): AccessToken
	{
		try {
			$helper = $this->facebook->getJavaScriptHelper();
			$accessToken = $helper->getAccessToken();

			if (!isset($accessToken)) {
				throw new FacebookLoginException('No cookie set or no OAuth data could be obtained from cookie.');
			}

			$accessToken = $this->getLongLifeValidatedToken($accessToken);
		} catch (FacebookResponseException | FacebookSDKException $e) {
			throw new FacebookLoginException($e->getMessage());
		}

		return $accessToken;
	}

	/**
	 * @param string $accessToken
	 * @param string[] $fields
	 * @return GraphUser
	 */
	public function getMe(string $accessToken, array $fields): GraphUser
	{
		try {
			// Fetch user data
			$me = $this->facebook->get('/me?fields=' . implode(',', $fields), $accessToken);
			return $me->getGraphUser();
		} catch (FacebookSDKException $e) {
			throw new FacebookLoginException($e->getMessage());
		}
	}

	/**
	 * @param AccessToken $accessToken
	 * @return AccessToken
	 */
	private function getLongLifeValidatedToken(AccessToken $accessToken): AccessToken
	{
		// Customer accepted our app
		$oAuth2Client = $this->facebook->getOAuth2Client();

		// Validate token
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		$tokenMetadata->validateAppId($this->facebook->getApp()->getId());
		$tokenMetadata->validateExpiration();

		// Exchanges a short-lived access token for a long-lived one
		if (!$accessToken->isLongLived()) {
			$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
		}

		return $accessToken;
	}

}
