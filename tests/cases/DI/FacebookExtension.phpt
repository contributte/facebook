<?php declare(strict_types = 1);

use Contributte\Facebook\DI\Nette\FacebookExtension;
use Contributte\Facebook\FacebookLogin;
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\Bridges\HttpDI\SessionExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test if FacebookLogin is created
test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('http', new HttpExtension())
			->addExtension('session', new SessionExtension())
			->addExtension('facebook', new FacebookExtension())
			->addConfig([
				'facebook' => [
					'appId' => 'd5sa4d5',
					'appSecret' => 'as5dd4sa6d54a6s5d4',
				],
			]);
	}, 1);
	/** @var Container $container */
	$container = new $class;

	// Service created
	Assert::type(FacebookLogin::class, $container->getService('facebook.login'));
});

// Test if FacebookLogin is created with Nette\DI\Statement
test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('http', new HttpExtension())
			->addExtension('session', new SessionExtension())
			->addExtension('facebook', new FacebookExtension())
			->addConfig([
				'facebook' => [
					'appId' => '@coolService::getFacebookAppId()',
					'appSecret' => '@coolService::getFacebookSecret()',
				],
			]);
	}, 1);
	/** @var Container $container */
	$container = new $class;

	// Service created
	Assert::type(FacebookLogin::class, $container->getService('facebook.login'));
});
