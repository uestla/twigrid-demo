<?php

use Nette\Forms\Form;
use TwiGrid\Components\Column;


class FullGrid extends BaseGrid
{

	/** @return void */
	protected function build()
	{
		$this->setTemplateFile(__DIR__ . '/@full.latte');

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name')->setSortable();
		$this->addColumn('surname', 'Surname')->setSortable();
		$this->addColumn('country_code', 'Country');
		$this->addColumn('birthday', 'Birthdate')->setSortable();
		$this->addColumn('kilograms', 'W (kg)')->setSortable();

		$this->setFilterFactory($this->createFilterContainer);
		$this->setDataLoader($this->dataLoader);
		$this->setPagination(12, $this->itemCounter);
		$this->setInlineEditing($this->createInlineEditContainer, $this->processInlineEditForm);

		$this->addRowAction('delete', 'Delete', $this->deleteRecord)
			->setConfirmation('Do you really want to delete this record?');

		$this->addGroupAction('delete', 'Delete', $this->deleteMany)
			->setConfirmation('WARNING! Deleted records cannot be restored! Proceed?');

		$this->setDefaultOrderBy(array(
			'surname' => Column::ASC,
			'firstname' => Column::DESC,
		));

		$this->setDefaultFilters(array(
			'kilograms' => 70,
			'birthday' => array(
				'min' => '01. 01. 1970',
				'max' => '28. 11. 1996',
			),
		));
	}


	/** @return Nette\Forms\Container */
	function createFilterContainer()
	{
		$container = new Nette\Forms\Container;

		$container->addText('firstname');
		$container->addText('surname');

		$birthday = $container->addContainer('birthday');
		$min = Helpers::addDateInput($birthday, 'min');
		$max = Helpers::addDateInput($birthday, 'max');

		$parser = callback('Helpers::parseDate');
		$min->addCondition(Form::FILLED)->addRule(function () use ($min, $max, $parser) {
			return !$max->filled
					|| (($minDt = $parser($min->value)) !== FALSE
						&& ($maxDt = $parser($max->value)) !== FALSE
						&& $minDt <= $maxDt);
		}, 'Please select valid date range.');

		$container->addSelect('country_code', 'Country', Helpers::getCountries())
				->setPrompt('---');

		$container->addText('kilograms')->addCondition(Form::FILLED)->addRule(Form::FLOAT);

		return $container;
	}


	/**
	 * @param  Nette\Database\Table\ActiveRow $record
	 * @return Nette\Forms\Container
	 */
	function createInlineEditContainer(Nette\Database\Table\ActiveRow $record)
	{
		$container = new Nette\Forms\Container;
		$container->addText('firstname')->setRequired();
		$container->addText('surname')->setRequired();
		$container->addSelect('country_code', 'Country', Helpers::getCountries())->setRequired()
				->setDefaultValue($record->country_code);
		Helpers::addDateInput($container, 'birthday')->setRequired();
		$container->addText('kilograms')->addRule(Form::FLOAT);
		$defaults = $record->toArray();
		$defaults['birthday'] = id(new DateTime($defaults['birthday']))->format('d. m. Y');
		return $container->setDefaults($defaults);
	}


	/**
	 * @param  FullGrid $grid
	 * @param  array $columns
	 * @param  array $filters
	 * @param  array $order
	 * @param  int $limit
	 * @param  int $offset
	 * @return Nette\Database\Table\Selection
	 */
	function dataLoader(FullGrid $grid, array $columns, array $filters, array $order, $limit, $offset)
	{
		// selection factory
		$users = $this->database->table('user');

		// columns
		$users->select(implode(', ', $columns));

		// filtering
		static::filterData($users, $columns, $filters);

		// order
		static::orderData($users, $order);

		// paginating
		return $users->limit($limit, $offset);
	}


	/**
	 * @param  FullGrid $grid
	 * @param  array $columns
	 * @param  array $filters
	 * @return int
	 */
	function itemCounter(FullGrid $grid, array $columns, array $filters)
	{
		return static::filterData($this->database->table('user'), $columns, $filters)
				->count('*');
	}


	/**
	 * @param  NSelection $data
	 * @param  array $columns
	 * @param  array $filters
	 * @return NSelection
	 */
	protected static function filterData(Nette\Database\Table\Selection $data, array $columns, array $filters)
	{
		foreach ($filters as $column => $value) {
			if ($column === 'gender') {
				$data->where($column, $value);

			} elseif ($column === 'country_code') {
				$data->where($column, $value);

			} elseif ($column === 'birthday') {
				isset($value['min']) && $data->where("$column >= ?", Helpers::parseDate($value['min'])->format('Y-m-d'));
				isset($value['max']) && $data->where("$column <= ?", Helpers::parseDate($value['max'])->format('Y-m-d'));

			} elseif ($column === 'kilograms') {
				$data->where("$column <= ?", $value);

			} elseif ($column === 'firstname' || $column === 'surname') {
				$data->where("$column LIKE ?", "$value%");

			} elseif (isset($columns[$column])) {
				$data->where("$column LIKE ?", "%$value%");
			}
		}

		return $data;
	}


	/**
	 * @param  NSelection $data
	 * @param  array $order
	 * @return NSelection
	 */
	protected static function orderData(Nette\Database\Table\Selection $data, array $order)
	{
		foreach ($order as $column => $dir) {
			$data->order($column . ($dir === TwiGrid\Components\Column::DESC ? ' DESC' : ''));
		}

		return $data;
	}


	/**
	 * @param  Nette\Database\Table\ActiveRow $record
	 * @return void
	 */
	function deleteRecord(Nette\Database\Table\ActiveRow $record)
	{
		$this->flashMessage("[DEMO] Deletion request sent for record '{$record->id}'.", 'success');
	}


	/**
	 * @param  Nette\Database\Table\ActiveRow $record
	 * @param  array $values
	 * @return void
	 */
	function processInlineEditForm(Nette\Database\Table\ActiveRow $record, array $values)
	{
		$this->flashMessage("[DEMO] Update request sent for record '{$record->id}'; new values: " . Nette\Utils\Json::encode($values), 'success');
	}


	/**
	 * @param  Nette\Database\Table\ActiveRow[]
	 * @return void
	 */
	function deleteMany(array $records)
	{
		$ids = array();
		foreach ($records as $record) {
			$ids[] = $record->id;
		}

		$this->flashMessage('[DEMO] Records deletion request : ' . Nette\Utils\Json::encode($ids), 'success');
	}

}
