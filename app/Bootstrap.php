<?php

declare(strict_types = 1);

use Nette\Configurator;
use Nette\DI\Container;
use Nette\Routing\Router;
use Nette\Application\Routers\RouteList;


final class Bootstrap
{

	public function boot(): Container
	{
		$configurator = new Configurator;
		$configurator->setTimeZone('UTC');
		$configurator->enableDebugger(__DIR__ . '/../log');
		$configurator->setTempDirectory(__DIR__ . '/../temp');
		$configurator->addConfig(__DIR__ . '/config.neon');
		$configurator->addParameters(['appDir' => __DIR__]);
		$configurator->createRobotLoader()->addDirectory(__DIR__)->register();

		$container = $configurator->createContainer();

		$router = new RouteList;
		$router->addRoute('? action=<action>', 'Example:homepage', Router::ONE_WAY);
		$router->addRoute('<action>', 'Example:homepage');
		$container->addService('router', $router);

		return $container;
	}

}
