<?php


class PaginationGrid extends TwiGrid\DataGrid
{

	/** @var Nette\Database\Connection */
	protected $connection;



	function __construct(Nette\Http\Session $s, Nette\Database\Connection $connection)
	{
		parent::__construct($s);
		$this->connection = $connection;

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name')->setSortable();
		$this->addColumn('surname', 'Surname')->setSortable();
		$this->addColumn('country_code', 'Country')->setSortable();

		$this->setPagination(12, $this->userCounter);
		$this->setDataLoader($this->dataLoader);
	}



	function userCounter(array $columns, array $filters)
	{
		return $this->connection->table('user')->count('*');
	}



	function dataLoader(PaginationGrid $grid, array $columns, array $filters, array $order, $limit, $offset)
	{
		$users = $this->connection->table('user');

		// columns
		$users->select(implode(', ', $columns));

		// sorting
		foreach ($order as $column => $desc) {
			$users->order($column . ($desc ? ' DESC' : ''));
		}

		// pagination
		$users->limit($limit, $offset);

		return $users;
	}

}
