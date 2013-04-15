<?php


class GroupActionGrid extends TwiGrid\DataGrid
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
		$this->addGroupAction('export', 'Export', function (array $ids) use ($me) {
			$me->flashMessage('Exporting items ' . Nette\Utils\Json::encode($ids), 'success');
		});

		$this->addGroupAction('delete', 'Delete', function (array $ids) use ($me) {
			$me->flashMessage('Deleting items ' . Nette\Utils\Json::encode($ids), 'success');

		}, 'Do you really want to delete all chosen items?');

		$this->setDataLoader($this->dataLoader);
	}



	function dataLoader(GroupActionGrid $grid, array $columns, array $filters, array $order)
	{
		return $this->connection->table('user')
			->select(implode(', ', $columns))
			->limit(12);
	}

}
