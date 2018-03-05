<?php declare(strict_types = 1);

namespace Contributte\Facebook;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Nette\Http\Session;

/**
 * Class FacebookFactory
 *
 * @author Filip Suska <vody105@gmail.com>
 */
class FacebookFactory
{

	/** @var string[] */
	private $config;

	/** @var Session */
	private $session;

	/**
	 * @param string[] $config
	 * @param Session $session
	 */
	public function __construct(array $config, Session $session)
	{
		$this->config = $config;
		$this->session = $session;
	}

	/**
	 * @return Facebook
	 * @throws FacebookSDKException
	 */
	public function create(): Facebook
	{
		if ($this->config['persistent_data_handler'] === 'session') {
			$this->session->start();
		}

		return new Facebook($this->config);
	}

}
