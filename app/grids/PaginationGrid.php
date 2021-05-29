<?php

declare(strict_types = 1);

use TwiGrid\DataGrid;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;


final class PaginationGrid extends DataGrid
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
		$this->addColumn('country_code', 'Country')->setSortable();

		$this->setPagination(12, function (array $filters): int {
			return $this->database->table('user')->count('*');
		});

		$this->setDataLoader(function (array $filters, array $order, int $limit, int $offset): Selection {
			$users = $this->database->table('user');

			// sorting
			foreach ($order as $column => $dir) {
				$users->order($column . ($dir === TwiGrid\Components\Column::DESC ? ' DESC' : ''));
			}

			// pagination
			$users->limit($limit, $offset);

			return $users;
		});

		$this->setRecordVariable('user');
	}

}
