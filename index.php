<?php

use Nette\Application\Routers;
use Nette\Application\Application as NApplication;

require_once __DIR__ . '/vendor/autoload.php';

$c = new Nette\Configurator;
$c->setTempDirectory(__DIR__ . '/temp');
$c->enableDebugger(__DIR__ . '/log');
$c->createRobotLoader()->addDirectory(array(__DIR__ . '/app'))->register();
$c->addConfig(__DIR__ . '/app/config.neon');

function id($a) { return $a; }

$container = $c->createContainer();
$router = $container->getService('router');
$router[] = new Routers\Route('simple', 'Example:sorting');
$router[] = new Routers\Route('<action>', 'Example:homepage');
$router[] = new Routers\SimpleRouter('Example:default');
$container->getByType(NApplication::class)->run();
