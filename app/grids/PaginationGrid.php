<?php


class PaginationGrid extends BaseGrid
{

	/** @return void */
	protected function build()
	{
		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name')->setSortable();
		$this->addColumn('surname', 'Surname')->setSortable();
		$this->addColumn('country_code', 'Country')->setSortable();

		$this->setPagination(12, $this->userCounter);
		$this->setDataLoader($this->dataLoader);
	}


	/**
	 * @param  PaginationGrid $grid
	 * @param  array $columns
	 * @param  array $filters
	 * @return Nette\Database\Table\Selection
	 */
	function userCounter(PaginationGrid $grid, array $columns, array $filters)
	{
		return $this->database->table('user')->count('*');
	}


	/**
	 * @param  PaginationGrid $grid
	 * @param  array $columns
	 * @param  array $filters
	 * @param  array $order
	 * @param  int $limit
	 * @param  int $offset
	 * @return Nette\Database\Table\Selection
	 */
	function dataLoader(PaginationGrid $grid, array $columns, array $filters, array $order, $limit, $offset)
	{
		$users = $this->database->table('user');

		// columns
		$users->select(implode(', ', $columns));

		// sorting
		foreach ($order as $column => $dir) {
			$users->order($column . ($dir === TwiGrid\Components\Column::DESC ? ' DESC' : ''));
		}

		// pagination
		$users->limit($limit, $offset);

		return $users;
	}

}
