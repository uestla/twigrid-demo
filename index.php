<?php

require_once __DIR__ . '/vendor/autoload.php';

$c = new Nette\Configurator;
$c->setTempDirectory(__DIR__ . '/temp');
$c->enableDebugger(__DIR__ . '/log');
$c->createRobotLoader()->addDirectory(array(__DIR__ . '/app'))->register();
$c->addConfig(__DIR__ . '/app/config.neon');

function id($a) { return $a; }
$cont = $c->createContainer();
$router = $cont->getService('router');
$router[] = new Nette\Application\Routers\Route('<action>', 'Example:default');
$router[] = new Nette\Application\Routers\SimpleRouter('Example:default');
$cont->getService('application')->run();
