<?php declare(strict_types = 1);

namespace Contributte\Facebook;

use League\OAuth2\Client\Provider\Facebook;
use Nette\Http\Session;

/**
 * Class FacebookFactory
 */
class FacebookFactory
{

	/** @var string[] */
	private $config;

	/** @var Session */
	private $session;

	/**
	 * @param string[] $config
	 */
	public function __construct(array $config, Session $session)
	{
		$this->config = $config;
		$this->session = $session;
	}

	/**
	 * @return Facebook
	 */
	public function create(): Facebook
	{
		if ($this->config['persistent_data_handler'] === 'session') {
			$this->session->start();
		}

		return new Facebook($this->config);
	}

}
