<?php


class GroupActionGrid extends TwiGrid\DataGrid
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

		$this->addGroupAction('export', 'Export', $this->exportMany);

		$this->addGroupAction('delete', 'Delete', $this->deleteMany)
			->setConfirmation('Do you really want to delete all chosen items?');

		$this->setDataLoader($this->dataLoader);
	}



	function dataLoader(GroupActionGrid $grid, array $columns, array $filters, array $order)
	{
		return $this->database->table('user')
			->select(implode(', ', $columns))
			->limit(12);
	}



	function exportMany(array $ids)
	{
		$this->flashMessage('[DEMO] Exporting items ' . Nette\Utils\Json::encode($ids), 'success');
	}



	function deleteMany(array $ids)
	{
		$this->flashMessage('[DEMO] Deleting items ' . Nette\Utils\Json::encode($ids), 'success');
	}

}
