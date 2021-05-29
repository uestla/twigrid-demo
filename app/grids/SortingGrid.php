<?php

declare(strict_types = 1);

use TwiGrid\DataGrid;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;


final class SortingGrid extends DataGrid
{

	private Explorer $database;


	public function __construct(Explorer $database)
	{
		parent::__construct();

		$this->database = $database;
	}


	protected function build(): void
	{
		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name')->setSortable();
		$this->addColumn('surname', 'Surname')->setSortable();
		$this->addColumn('streetaddress', 'Street address');
		$this->addColumn('birthday', 'Birthdate')->setSortable();
		$this->addColumn('kilograms', 'Weight (kg)')->setSortable();

		$this->setDataLoader(function (array $filters, array $order): Selection {
			$users = $this->database->table('user');

			// sorting
			foreach ($order as $column => $dir) {
				$users->order($column . ($dir === TwiGrid\Components\Column::DESC ? ' DESC' : ''));
			}

			return $users->limit(12);
		});

		$this->setRecordVariable('user');
	}

}
