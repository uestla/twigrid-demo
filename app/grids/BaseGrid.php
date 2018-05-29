<?php

use Nette\Database\Context as NdbContext;


abstract class BaseGrid extends TwiGrid\DataGrid
{

	/** @var NdbContext */
	protected $database;


	/** @param  NdbContext $database */
	public function __construct(NdbContext $database)
	{
		parent::__construct();
		$this->database = $database;
	}


	/** @return void */
	protected function build()
	{
		parent::build();
		$this->setRecordVariable('user');
	}

}
