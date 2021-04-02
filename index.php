<?php

use Nette\Routing\Router;
use Nette\Application\Application;
use Nette\Application\Routers\RouteList;

require_once __DIR__ . '/vendor/autoload.php';

$configurator = new Nette\Configurator;
$configurator->setTimeZone('UTC');
$configurator->enableDebugger(__DIR__ . '/log');
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->addConfig(__DIR__ . '/app/config.neon');
$configurator->addParameters(['appDir' => __DIR__ . '/app']);
$configurator->createRobotLoader()->addDirectory(__DIR__ . '/app')->register();

$container = $configurator->createContainer();

$router = new RouteList;
$router->addRoute('? action=<action>', 'Example:homepage', Router::ONE_WAY);
$router->addRoute('sorting', 'Example:sorting');
$router->addRoute('<action>', 'Example:homepage');
$container->addService('router', $router);

$container->getByType(Application::class)->run();
