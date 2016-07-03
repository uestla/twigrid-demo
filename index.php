<?php

use Nette\Application as NApplication;

require_once __DIR__ . '/vendor/autoload.php';

$configurator = new Nette\Configurator;
$configurator->setTimeZone('UTC');
$configurator->enableDebugger(__DIR__ . '/log');
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->addConfig(__DIR__ . '/app/config.neon');
$configurator->addParameters(['appDir' => __DIR__ . '/app']);
$configurator->createRobotLoader()->addDirectory(__DIR__ . '/app')->register();


$container = $configurator->createContainer();

$router = $container->getByType(NApplication\IRouter::class);
$router[] = new NApplication\Routers\Route('? action=<action>', 'Example:homepage', NApplication\Routers\Route::ONE_WAY);
$router[] = new NApplication\Routers\Route('simple', 'Example:sorting');
$router[] = new NApplication\Routers\Route('<action>', 'Example:homepage');
$router[] = new NApplication\Routers\SimpleRouter('Example:default');

$container->getByType(NApplication\Application::class)->run();
