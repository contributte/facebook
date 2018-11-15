<?php declare(strict_types = 1);

namespace Contributte\Facebook\DI\Nette;

use Contributte\Facebook\FacebookFactory;
use Contributte\Facebook\FacebookLogin;
use Nette\DI\CompilerExtension;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;

/**
 * Class FacebookExtension
 */
class FacebookExtension extends CompilerExtension
{

	/** @var mixed[]  */
	private $defaults = [
		'appId' => null,
		'appSecret' => null,
		'defaultGraphVersion' => null,
		'persistentDataHandler' => 'session',
	];

	/**
	 * @throws AssertionException
	 */
	public function loadConfiguration(): void
	{
		$config = $this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		Validators::assertField($config, 'appId', 'string|number|Nette\DI\Statement');
		Validators::assertField($config, 'appSecret', 'string|number|Nette\DI\Statement');
		Validators::assertField($config, 'persistentDataHandler', 'string');

		$appData = [
			'app_id' => $config['appId'],
			'app_secret' => $config['appSecret'],
			'persistent_data_handler' => $config['persistentDataHandler'],
		];

		// Facebook has its own default value for default_graph_version
		if ($config['defaultGraphVersion'] !== null) {
			$appData['default_graph_version'] = $config['defaultGraphVersion'];
		}

		$builder->addDefinition($this->prefix('facebookFactory'))
			->setType(FacebookFactory::class)
			->setArguments([$appData]);

		$builder->addDefinition($this->prefix('facebook'))
			->setFactory('@' . $this->prefix('facebookFactory') . '::create');

		$builder->addDefinition($this->prefix('login'))
			->setType(FacebookLogin::class);
	}

}
