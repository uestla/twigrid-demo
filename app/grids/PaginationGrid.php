<?php


class PaginationGrid extends BaseGrid
{

	protected function build()
	{
		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name')->setSortable();
		$this->addColumn('surname', 'Surname')->setSortable();
		$this->addColumn('country_code', 'Country')->setSortable();

		$this->setPagination(12, $this->userCounter);
		$this->setDataLoader($this->dataLoader);
	}


	function userCounter(array $columns, array $filters)
	{
		return $this->database->table('user')->count('*');
	}


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
