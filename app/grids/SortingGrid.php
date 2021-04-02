<?php

declare(strict_types = 1);


class SortingGrid extends BaseGrid
{

	protected function build(): void
	{
		parent::build();

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name')->setSortable();
		$this->addColumn('surname', 'Surname')->setSortable();
		$this->addColumn('streetaddress', 'Street address');
		$this->addColumn('birthday', 'Birthdate')->setSortable();
		$this->addColumn('kilograms', 'Weight (kg)')->setSortable();

		$this->setDataLoader(function (array $filters, array $order) {
			$users = $this->database->table('user');

			// sorting
			foreach ($order as $column => $dir) {
				$users->order($column . ($dir === TwiGrid\Components\Column::DESC ? ' DESC' : ''));
			}

			return $users->limit(12);
		});
	}

}
