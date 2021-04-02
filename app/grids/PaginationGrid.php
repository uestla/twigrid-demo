<?php

declare(strict_types = 1);


use Nette\Database\Table\Selection;


class PaginationGrid extends BaseGrid
{

	protected function build(): void
	{
		parent::build();

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name')->setSortable();
		$this->addColumn('surname', 'Surname')->setSortable();
		$this->addColumn('country_code', 'Country')->setSortable();

		$this->setPagination(12, [$this, 'userCounter']);
		$this->setDataLoader([$this, 'dataLoader']);
	}


	/** @param  array<string, mixed> $filters */
	public function userCounter(array $filters): int
	{
		return $this->database->table('user')->count('*');
	}


	/**
	 * @param  array<string, mixed> $filters
	 * @param  array<string, bool> $order
	 */
	public function dataLoader(array $filters, array $order, int $limit, int $offset): Selection
	{
		$users = $this->database->table('user');

		// sorting
		foreach ($order as $column => $dir) {
			$users->order($column . ($dir === TwiGrid\Components\Column::DESC ? ' DESC' : ''));
		}

		// pagination
		$users->limit($limit, $offset);

		return $users;
	}

}
