<?php

declare(strict_types = 1);

use Nette\Database\Explorer;


abstract class BaseGrid extends TwiGrid\DataGrid
{

	protected Explorer $database;


	public function __construct(Explorer $database)
	{
		parent::__construct();

		$this->database = $database;
	}


	protected function build(): void
	{
		parent::build();
		$this->setRecordVariable('user');
	}

}
