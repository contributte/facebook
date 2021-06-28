<?php declare(strict_types = 1);

namespace Contributte\Facebook\DI\Nette;

use Contributte\Facebook\FacebookFactory;
use Contributte\Facebook\FacebookLogin;
use Nette\DI\CompilerExtension;
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
			'clientId' => Expect::string()->required(),
			'clientSecret' => Expect::string()->required(),
			'graphApiVersion' => Expect::string()->required()
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		$appData = [
			'clientId' => $config->clientId,
			'clientSecret' => $config->clientSecret,
			'graphApiVersion' => $config->graphApiVersion,
		];

		$builder->addDefinition($this->prefix('facebookFactory'))
			->setType(FacebookFactory::class)
			->setArguments([$appData]);

		$builder->addDefinition($this->prefix('facebook'))
			->setFactory('@' . $this->prefix('facebookFactory') . '::create');

		$builder->addDefinition($this->prefix('login'))
			->setType(FacebookLogin::class);
	}

}
