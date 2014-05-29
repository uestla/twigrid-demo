<?php

use Nette\Http\Session as NSession;
use Nette\Database\Context as NdbContext;


abstract class BaseGrid extends TwiGrid\DataGrid
{

	/** @var NdbContext */
	protected $database;


	/**
	 * @param  NSession $session
	 * @param  NdbContext $database
	 */
	function __construct(NSession $s, NdbContext $database)
	{
		parent::__construct($s);
		$this->database = $database;
	}

}
