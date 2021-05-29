<?php

declare(strict_types = 1);

use Nette\Forms\Form;
use Nette\Utils\Json;
use TwiGrid\DataGrid;
use Nette\Forms\Container;
use Nette\Database\Explorer;
use TwiGrid\Components\Column;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;


final class FullGrid extends DataGrid
{

	private Explorer $database;


	public function __construct(Explorer $database)
	{
		parent::__construct();

		$this->database = $database;
	}


	protected function build(): void
	{
		$this->setTemplateFile(__DIR__ . '/@full.latte');

		// columns & sorting
		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name')->setSortable();
		$this->addColumn('surname', 'Surname')->setSortable();
		$this->addColumn('country_code', 'Country');
		$this->addColumn('birthday', 'Birthdate')->setSortable();
		$this->addColumn('kilograms', 'W (kg)')->setSortable();

		$this->setDefaultOrderBy([
			'surname' => Column::ASC,
			'firstname' => Column::DESC,
		]);

		// filtering
		$this->setFilterFactory(static function (Container $container): void {
			$container->addText('firstname');
			$container->addText('surname');

			$birthday = $container->addContainer('birthday');
			$min = Helpers::addDateInput($birthday, 'min');
			$max = Helpers::addDateInput($birthday, 'max');

			$min->addCondition(Form::FILLED)->addRule(static function () use ($min, $max): bool {
				return !$max->filled
					|| (($minDate = Helpers::parseDate($min->value)) !== null
						&& ($maxDate = Helpers::parseDate($max->value)) !== null
						&& $minDate <= $maxDate);

			}, 'Please select valid date range.');

			$container->addSelect('country_code', 'Country', Helpers::getCountries())
				->setPrompt('---');

			$container->addText('kilograms')->addCondition(Form::FILLED)->addRule(Form::FLOAT);
		});

		$this->setDefaultFilters([
			'kilograms' => 70.0,
			'birthday' => [
				'min' => '01. 01. 1970',
				'max' => '28. 11. 1996',
			],
		]);

		// pagination
		$this->setPagination(12, function (array $filters): int {
			return self::filterData($this->database->table('user'), $filters)
				->count('*');
		});

		// data loading
		$this->setDataLoader(function (array $filters, array $order, int $limit, int $offset): Selection {
			// selection factory
			$users = $this->database->table('user');

			// filtering
			self::filterData($users, $filters);

			// order
			foreach ($order as $column => $dir) {
				$users->order($column . ($dir === Column::DESC ? ' DESC' : ''));
			}

			// paginating
			return $users->limit($limit, $offset);
		});

		// inline editing
		$this->setInlineEditing(static function (Container $container, ActiveRow $user): void {
			$container->addText('firstname')->setRequired();
			$container->addText('surname')->setRequired();
			$container->addSelect('country_code', 'Country', Helpers::getCountries())
				->setRequired()
				->setDefaultValue($user->country_code);

			Helpers::addDateInput($container, 'birthday')->setRequired();
			$container->addText('kilograms')->addRule(Form::FLOAT);
			$defaults = $user->toArray();
			$defaults['birthday'] = (new \DateTime($defaults['birthday']))->format('d. m. Y');

			$container->setDefaults($defaults);

		}, function (ActiveRow $user, array $values): void {
			$this->flashMessage("[DEMO] Update request sent for record '{$user->id}'; new values: " . Json::encode($values), 'success');
		});

		$this->setRecordVariable('user');

		// actions
		$this->addRowAction('delete', 'Delete', function (ActiveRow $user): void {
				$this->flashMessage("[DEMO] Deletion request sent for record '{$user->id}'.", 'success');
			})
			->setConfirmation('Do you really want to delete this record?');

		$this->addGroupAction('delete', 'Delete', function (array $users): void {
				$IDs = [];
				foreach ($users as $user) {
					$IDs[] = $user->id;
				}

				$this->flashMessage('[DEMO] Records deletion request : ' . Json::encode($IDs), 'success');
			})
			->setConfirmation('WARNING! Deleted records cannot be restored! Proceed?');
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
				if (isset($value['min']) && ($minDate = Helpers::parseDate($value['min'])) !== null) {
					$selection->where("$column >= ?", $minDate->format('Y-m-d'));
				}

				if (isset($value['max']) && ($maxDate = Helpers::parseDate($value['max'])) !== null) {
					$selection->where("$column <= ?", $maxDate->format('Y-m-d'));
				}

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

}
