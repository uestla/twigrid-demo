<?php

declare(strict_types = 1);

use Nette\Forms\Form;
use TwiGrid\DataGrid;
use Nette\Forms\Container;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;


final class FilterGrid extends DataGrid
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
		$this->addColumn('kilograms', 'Weight (kg)')->setSortable();

		$this->setFilterFactory(static function (Container $container): void {
			$container->addText('firstname');
			$container->addText('surname');

			$container->addText('kilograms')
				->addCondition(Form::FILLED)
				->addRule(Form::FLOAT);
		});

		$this->setDataLoader(function (array $filters, array $order): Selection {
			$users = $this->database->table('user');

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
		});

		$this->setRecordVariable('user');
	}

}
