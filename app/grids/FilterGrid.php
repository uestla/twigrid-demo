<?php

use Nette\Forms\Form;


class FilterGrid extends TwiGrid\DataGrid
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
		$this->addColumn('kilograms', 'Weight (kg)')->setSortable();

		$this->setFilterFactory($this->filterFactory);
		$this->setDataLoader($this->dataLoader);
	}



	function filterFactory()
	{
		$c = new Nette\Forms\Container;

		$c->addText('firstname');
		$c->addText('surname');

		$c->addText('kilograms')
			->addCondition(Form::FILLED)
				->addRule(Form::FLOAT);

		return $c;
	}



	function dataLoader(FilterGrid $grid, array $columns, array $filters, array $order)
	{
		$users = $this->connection->table('user');

		// columns
		$users->select(implode(', ', $columns));

		// filtering
		foreach ($filters as $column => $value) {
			if ($column === 'kilograms') {
				$users->where("$column <= ?", $value);

			} else {
				$users->where("$column LIKE ?", "$value%");
			}
		}

		// sorting
		foreach ($order as $column => $dir) {
			$users->order($column . ($dir === TwiGrid\Components\Column::DESC ? ' DESC' : ''));
		}

		return $users->limit(12);
	}

}
