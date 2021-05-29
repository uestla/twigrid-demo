<?php

declare(strict_types = 1);

use Nette\Application\Application;

require_once __DIR__ . '/vendor/autoload.php';


(new Bootstrap)
	->boot()
	->getByType(Application::class)
	->run();
