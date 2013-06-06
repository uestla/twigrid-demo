<?php


class SimpleGrid extends TwiGrid\DataGrid
{

	function __construct(Nette\Http\Session $s, Nette\Database\Connection $connection)
	{
		parent::__construct($s);

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name')->setSortable();
		$this->addColumn('surname', 'Surname')->setSortable();
		$this->addColumn('streetaddress', 'Street address');
		$this->addColumn('birthday', 'Birthdate')->setSortable();
		$this->addColumn('kilograms', 'Weight (kg)')->setSortable();

		$this->setDataLoader(function ($grid, array $columns, array $filters, array $order) use ($connection) {
			$users = $connection->table('user');

			// columns
			$users->select(implode(', ', $columns));

			// sorting
			foreach ($order as $column => $dir) {
				$users->order($column . ($dir === TwiGrid\Components\Column::DESC ? ' DESC' : ''));
			}

			return $users->limit(12);
		});
	}

}
