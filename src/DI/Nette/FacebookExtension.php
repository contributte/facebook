<?php declare(strict_types = 1);

namespace Contributte\Facebook\DI\Nette;

use Contributte\Facebook\FacebookFactory;
use Contributte\Facebook\FacebookLogin;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
class FacebookExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'appId' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))->required(),
			'appSecret' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))->required(),
			'defaultGraphVersion' => Expect::string(),
			'persistentDataHandler' => Expect::string('session'),
			'httpClientHandler' => Expect::mixed()
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		$appData = [
			'app_id' => $config->appId,
			'app_secret' => $config->appSecret,
			'persistent_data_handler' => $config->persistentDataHandler,
		];

		// Facebook has its own default value for default_graph_version
		if ($config->defaultGraphVersion !== null) {
			$appData['default_graph_version'] = $config->defaultGraphVersion;
		}

		if ($config->httpClientHandler !== null) {
			$appData['http_client_handler'] = $config->httpClientHandler;
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
