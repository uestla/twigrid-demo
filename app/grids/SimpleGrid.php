<?php


class SimpleGrid extends BaseGrid
{

	/** @return void */
	protected function build()
	{
		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name')->setSortable();
		$this->addColumn('surname', 'Surname')->setSortable();
		$this->addColumn('streetaddress', 'Street address');
		$this->addColumn('birthday', 'Birthdate')->setSortable();
		$this->addColumn('kilograms', 'Weight (kg)')->setSortable();

		$db = $this->database;

		$this->setDataLoader(function (SimpleGrid $grid, array $columns, array $filters, array $order) use ($db) {
			$users = $db->table('user');

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
