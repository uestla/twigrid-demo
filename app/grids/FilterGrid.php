<?php

declare(strict_types = 1);

use Nette\Forms\Form;
use Nette\Forms\Container;
use Nette\Database\Table\Selection;


class FilterGrid extends BaseGrid
{

	protected function build(): void
	{
		parent::build();

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name')->setSortable();
		$this->addColumn('surname', 'Surname')->setSortable();
		$this->addColumn('kilograms', 'Weight (kg)')->setSortable();

		$this->setFilterFactory([$this, 'filterFactory']);
		$this->setDataLoader([$this, 'dataLoader']);
	}


	public function filterFactory(Container $container): void
	{
		$container->addText('firstname');
		$container->addText('surname');

		$container->addText('kilograms')
			->addCondition(Form::FILLED)
				->addRule(Form::FLOAT);
	}


	/**
	 * @param  array<string, mixed> $filters
	 * @param  array<string, bool> $order
	 */
	public function dataLoader(array $filters, array $order): Selection
	{
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
	}

}
