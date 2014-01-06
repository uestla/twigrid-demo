<?php


class RowActionGrid extends TwiGrid\DataGrid
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

		$this->addRowAction('download', 'Download', $this->downloadItem)
			->setProtected(FALSE); // turns of the CSRF protection (not necessary here)

		$this->addRowAction('delete', 'Delete', $this->deleteItem)
			->setConfirmation('Do you really want to delete this item?');

		$this->setDataLoader($this->dataLoader);
	}



	function dataLoader(RowActionGrid $grid, array $columns, array $filters, array $order)
	{
		return $this->dbContext->table('user')
			->select(implode(', ', $columns))
			->limit(12);
	}



	function downloadItem($id)
	{
		$this->flashMessage("Downloading item '$id'...", 'success');
	}



	function deleteItem($id)
	{
		$this->flashMessage("Deleting item '$id'...", 'success');
	}

}
