<?php

declare(strict_types = 1);

use Nette\Forms\Form;
use Nette\Utils\Json;
use Nette\Forms\Container;
use TwiGrid\Components\Column;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;


class FullGrid extends BaseGrid
{

	protected function build(): void
	{
		parent::build();

		$this->setTemplateFile(__DIR__ . '/@full.latte');

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name')->setSortable();
		$this->addColumn('surname', 'Surname')->setSortable();
		$this->addColumn('country_code', 'Country');
		$this->addColumn('birthday', 'Birthdate')->setSortable();
		$this->addColumn('kilograms', 'W (kg)')->setSortable();

		$this->setFilterFactory([$this, 'createFilterContainer']);
		$this->setDataLoader([$this, 'dataLoader']);
		$this->setPagination(12, [$this, 'itemCounter']);
		$this->setInlineEditing([$this, 'createInlineEditContainer'], [$this, 'processInlineEditForm']);

		$this->addRowAction('delete', 'Delete', [$this, 'deleteRecord'])
			->setConfirmation('Do you really want to delete this record?');

		$this->addGroupAction('delete', 'Delete', [$this, 'deleteMany'])
			->setConfirmation('WARNING! Deleted records cannot be restored! Proceed?');

		$this->setDefaultOrderBy([
			'surname' => Column::ASC,
			'firstname' => Column::DESC,
		]);

		$this->setDefaultFilters([
			'kilograms' => 70,
			'birthday' => [
				'min' => '01. 01. 1970',
				'max' => '28. 11. 1996',
			],
		]);
	}


	public function createFilterContainer(Container $container): void
	{
		$container->addText('firstname');
		$container->addText('surname');

		$birthday = $container->addContainer('birthday');
		$min = Helpers::addDateInput($birthday, 'min');
		$max = Helpers::addDateInput($birthday, 'max');

		$min->addCondition(Form::FILLED)->addRule(function () use ($min, $max) {
			return !$max->filled
					|| (($minDt = Helpers::parseDate($min->value)) !== null
						&& ($maxDt = Helpers::parseDate($max->value)) !== null
						&& $minDt <= $maxDt);

		}, 'Please select valid date range.');

		$container->addSelect('country_code', 'Country', Helpers::getCountries())
				->setPrompt('---');

		$container->addText('kilograms')->addCondition(Form::FILLED)->addRule(Form::FLOAT);
	}


	public function createInlineEditContainer(Container $container, ActiveRow $record): void
	{
		$container->addText('firstname')->setRequired();
		$container->addText('surname')->setRequired();
		$container->addSelect('country_code', 'Country', Helpers::getCountries())
				->setRequired()
				->setDefaultValue($record->country_code);

		Helpers::addDateInput($container, 'birthday')->setRequired();
		$container->addText('kilograms')->addRule(Form::FLOAT);
		$defaults = $record->toArray();
		$defaults['birthday'] = (new DateTime($defaults['birthday']))->format('d. m. Y');

		$container->setDefaults($defaults);
	}


	/**
	 * @param  array<string, mixed> $filters
	 * @param  array<string, bool> $order
	 */
	public function dataLoader(array $filters, array $order, int $limit, int $offset): Selection
	{
		// selection factory
		$users = $this->database->table('user');

		// filtering
		static::filterData($users, $filters);

		// order
		static::orderData($users, $order);

		// paginating
		return $users->limit($limit, $offset);
	}


	/** @param  array<string, mixed> $filters */
	public function itemCounter(array $filters): int
	{
		return static::filterData($this->database->table('user'), $filters)
				->count('*');
	}


	/** @param  array<string, mixed> $filters */
	protected static function filterData(Selection $selection, array $filters): Selection
	{
		foreach ($filters as $column => $value) {
			if ($column === 'gender') {
				$selection->where($column, $value);

			} elseif ($column === 'country_code') {
				$selection->where($column, $value);

			} elseif ($column === 'birthday') {
				isset($value['min']) && $selection->where("$column >= ?", Helpers::parseDate($value['min'])->format('Y-m-d'));
				isset($value['max']) && $selection->where("$column <= ?", Helpers::parseDate($value['max'])->format('Y-m-d'));

			} elseif ($column === 'kilograms') {
				$selection->where("$column <= ?", $value);

			} elseif ($column === 'firstname' || $column === 'surname') {
				$selection->where("$column LIKE ?", "$value%");

			} else {
				$selection->where("$column LIKE ?", "%$value%");
			}
		}

		return $selection;
	}


	/** @param  array<string, bool> $order */
	protected static function orderData(Selection $data, array $order): Selection
	{
		foreach ($order as $column => $dir) {
			$data->order($column . ($dir === TwiGrid\Components\Column::DESC ? ' DESC' : ''));
		}

		return $data;
	}


	public function deleteRecord(ActiveRow $record): void
	{
		$this->flashMessage("[DEMO] Deletion request sent for record '{$record->id}'.", 'success');
	}


	/** @param  array<string, mixed> $values */
	public function processInlineEditForm(ActiveRow $record, array $values): void
	{
		$this->flashMessage("[DEMO] Update request sent for record '{$record->id}'; new values: " . Json::encode($values), 'success');
	}


	/** @param  ActiveRow[] $records */
	public function deleteMany(array $records): void
	{
		$ids = [];
		foreach ($records as $record) {
			$ids[] = $record->id;
		}

		$this->flashMessage('[DEMO] Records deletion request : ' . Json::encode($ids), 'success');
	}

}
