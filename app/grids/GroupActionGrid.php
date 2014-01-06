<?php


class GroupActionGrid extends TwiGrid\DataGrid
{

	/** @var Nette\Database\Context */
	private $dbContext;



	function __construct(Nette\Http\Session $s, Nette\Database\Context $dbContext)
	{
		parent::__construct($s);
		$this->dbContext = $dbContext;

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name');
		$this->addColumn('surname', 'Surname');
		$this->addColumn('country_code', 'Country');
		$this->addColumn('birthday', 'Birthdate');

		$this->addGroupAction('export', 'Export', $this->exportMany);

		$this->addGroupAction('delete', 'Delete', $this->deleteMany)
			->setConfirmation('Do you really want to delete all chosen items?');

		$this->setDataLoader($this->dataLoader);
	}



	function dataLoader(GroupActionGrid $grid, array $columns, array $filters, array $order)
	{
		return $this->dbContext->table('user')
			->select(implode(', ', $columns))
			->limit(12);
	}



	function exportMany(array $ids)
	{
		$this->flashMessage('Exporting items ' . Nette\Utils\Json::encode($ids), 'success');
	}



	function deleteMany(array $ids)
	{
		$this->flashMessage('Deleting items ' . Nette\Utils\Json::encode($ids), 'success');
	}

}
