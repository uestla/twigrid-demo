<?php


class PresenterFactory extends Nette\Application\PresenterFactory
{
	/** @var string */
	protected $appDir;



	function __construct($appDir, \Nette\DI\Container $container)
	{
		parent::__construct($appDir, $container);

		$this->appDir = (string) $appDir;
	}



	function formatPresenterFile($presenter)
	{
		return $this->appDir . '/Presenter.php';
	}
}
