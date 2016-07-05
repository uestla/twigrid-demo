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
	public function __construct(NSession $session, NdbContext $database)
	{
		parent::__construct($session);
		$this->database = $database;
	}

}
