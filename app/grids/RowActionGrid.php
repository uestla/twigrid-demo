<?php


class RowActionGrid extends TwiGrid\DataGrid
{

	/** @var Nette\Database\Connection */
	protected $connection;



	function __construct(Nette\Http\Session $s, Nette\Database\Connection $connection)
	{
		parent::__construct($s);
		$this->connection = $connection;

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name');
		$this->addColumn('surname', 'Surname');
		$this->addColumn('country_code', 'Country');
		$this->addColumn('birthday', 'Birthdate');

		$me = $this;
		$this->addRowAction('download', 'Download', function ($id) use ($me) {
			$me->flashMessage("Downloading item '$id'...", 'success');
		});

		$this->addRowAction('delete', 'Delete', function ($id) use ($me) {
			$me->flashMessage("Deleting item '$id'...", 'success');

		}, 'Do you really want to delete this item?');

		$this->setDataLoader($this->dataLoader);
	}



	function dataLoader(RowActionGrid $grid, array $columns, array $filters, array $order)
	{
		return $this->connection->table('user')
			->select(implode(', ', $columns))
			->limit(12);
	}

}
