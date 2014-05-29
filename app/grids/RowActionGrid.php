<?php


class RowActionGrid extends TwiGrid\DataGrid
{

	/** @var Nette\Database\Context @inject */
	public $database;


	protected function build()
	{
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
		return $this->database->table('user')
			->select(implode(', ', $columns))
			->limit(12);
	}


	function downloadItem($id)
	{
		$this->flashMessage("[DEMO] Downloading item '$id'...", 'success');
	}


	function deleteItem($id)
	{
		$this->flashMessage("[DEMO] Deleting item '$id'...", 'success');
	}

}
