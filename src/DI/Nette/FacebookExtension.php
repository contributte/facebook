<?php declare(strict_types = 1);

namespace Contributte\Facebook\DI\Nette;

use Contributte\Facebook\FacebookLogin;
use Facebook\Facebook;
use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use Nette\Utils\Validators;

/**
 * Class FacebookExtension
 *
 * @author Filip Suska <filipsuska@gmail.com>
 */
class FacebookExtension extends CompilerExtension
{

	/** @var string[]  */
	private $defaults = [
		'appId' => NULL,
		'appSecret' => NULL,
	];

	/**
	 * @return void
	 */
	public function loadConfiguration(): void
	{
		$config = $this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		Validators::assertField($config, 'appId', 'string|number');
		Validators::assertField($config, 'appSecret', 'string|number');

		$appData = [
			'app_id' => $config['appId'],
			'app_secret' => $config['appSecret'],
		];

		$builder->addDefinition($this->prefix('login'))
			->setClass(FacebookLogin::class, [
				new Statement(Facebook::class, [$appData]),
			]);
	}

}
