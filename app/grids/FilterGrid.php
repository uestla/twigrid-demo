<?php

use Nette\Forms\Form;
use Nette\Forms\Container;


class FilterGrid extends BaseGrid
{

	/** @return void */
	protected function build()
	{
		parent::build();

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name')->setSortable();
		$this->addColumn('surname', 'Surname')->setSortable();
		$this->addColumn('kilograms', 'Weight (kg)')->setSortable();

		$this->setFilterFactory([$this, 'filterFactory']);
		$this->setDataLoader([$this, 'dataLoader']);
	}


	/**
	 * @param  Container $container
	 * @return void
	 */
	public function filterFactory(Container $container)
	{
		$container->addText('firstname');
		$container->addText('surname');

		$container->addText('kilograms')
			->addCondition(Form::FILLED)
				->addRule(Form::FLOAT);
	}


	/**
	 * @param  array $filters
	 * @param  array $order
	 * @return Nette\Database\Table\Selection
	 */
	public function dataLoader(array $filters, array $order)
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
