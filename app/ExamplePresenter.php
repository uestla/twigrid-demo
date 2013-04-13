<?php

use Nette\Forms\Form;


class ExamplePresenter extends Nette\Application\UI\Presenter
{

	/** @persistent bool */
	public $showQueries = FALSE;

	/** @var Nette\Database\Connection */
	protected $ndb;

	/** @var Nette\Caching\Cache */
	protected $cache;



	// === DATAGRID DEFINITION ==================================================================

	protected function createComponentDataGrid()
	{
		$grid = $this->context->createDataGrid();
		$grid->setTemplateFile(__DIR__ . '/user-grid.latte');

		$grid->addColumn('firstname', 'Name')->setSortable();
		$grid->addColumn('surname', 'Surname')->setSortable();
		$grid->addColumn('country_code', 'Country');
		$grid->addColumn('birthday', 'Birthdate')->setSortable();
		$grid->addColumn('kilograms', 'Weight (kg)')->setSortable();

		$grid->setPrimaryKey('id');
		$grid->setFilterFactory($this->createFilterContainer);
		$grid->setDataLoader($this->dataLoader);

		$grid->setInlineEditing($this->createInlineEditContainer, $this->processInlineEditForm);
		$grid->addRowAction('delete', 'Delete', $this->deleteRecord, 'Do you really want to delete this record?');
		$grid->addGroupAction('edit', 'Delete', $this->deleteMany, 'WARNING! Deleted records cannot be restored! Proceed?');

		$grid->setDefaultOrderBy('surname');
		$grid->setDefaultFilters(array(
			'kilograms' => 70,
			'birthday' => array(
				'min' => '15. 01. 1961',
				'max' => '28. 11. 1996',
			),
		));

		return $grid;
	}



	function createFilterContainer()
	{
		$container = new Nette\Forms\Container;

		$container->addText('firstname');
		$container->addText('surname');

		$birthday = $container->addContainer('birthday');
		$min = Helpers::addDateInput( $birthday, 'min' );
		$max = Helpers::addDateInput( $birthday, 'max' );

		$parser = callback('Helpers::parseDate');
		$min->addCondition(Form::FILLED)->addRule(function () use ($min, $max, $parser) {
			return !$max->filled
					|| (($minDt = $parser($min->value)) !== FALSE
						&& ($maxDt = $parser($max->value)) !== FALSE
						&& $minDt <= $maxDt);
		}, 'Please select valid date range.');

		$container->addSelect('country_code', 'Country', Helpers::getCountries())
				->setPrompt('---');

		$container->addText('kilograms')->addCondition( Form::FILLED )->addRule( Form::FLOAT );

		return $container;
	}



	function createInlineEditContainer($record)
	{
		$container = new Nette\Forms\Container;
		$container->addText('firstname')->setRequired();
		$container->addText('surname')->setRequired();
		$container->addSelect('country_code', 'Country', Helpers::getCountries())->setRequired()
				->setDefaultValue($record->country_code);
		Helpers::addDateInput($container, 'birthday')->setRequired();
		$container->addText('kilograms')->addRule(Form::FLOAT);
		$defaults = $record->toArray();
		$defaults['birthday'] = id( new DateTime($defaults['birthday']) )->format('d. m. Y');
		return $container->setDefaults( $defaults );
	}



	function dataLoader(TwiGrid\DataGrid $grid, array $columns, array $order, array $filters)
	{
		// selection factory
		$users = $this->ndb->table('user');

		// columns
		$users->select( implode(', ', $columns) );

		// order result
		foreach ($order as $column => $desc) {
			$users->order( $column . ($desc ? ' DESC' : '') );
		}

		// filter result
		$conds = array();
		foreach ($filters as $column => $value) {
			if ($column === 'gender') {
				$conds[ $column ] = $value;

			} elseif ($column === 'country_code') {
				$conds[$column] = $value;

			} elseif ($column === 'birthday') {
				isset($value['min']) && $conds["$column >= ?"] = Helpers::parseDate( $value['min'] )->format('Y-m-d');
				isset($value['max']) && $conds["$column <= ?"] = Helpers::parseDate( $value['max'] )->format('Y-m-d');

			} elseif ($column === 'kilograms') {
				$conds["$column <= ?"] = $value;

			} elseif ($column === 'firstname' || $column === 'surname') {
				$conds["$column LIKE ?"] = "$value%";

			} elseif (isset($columns[$column])) {
				$conds["$column LIKE ?"] = "%$value%";
			}
		}

		return $users->where($conds)->limit(12);
	}



	// === DATA MANIPULATIONS ===============================================================

	function deleteRecord($id)
	{
		// $this->ndb->table('user')->find($id)->delete();
		$this->flashMessage("Deletion request sent for record '$id'.", 'warning');
		// !$this->isAjax() && $this->redirect('this'); // intentionally not redirecting (it's in the grid call)
	}



	function processInlineEditForm($id, array $values)
	{
		// $this->ndb->table('user')->find($id)->update($values);
		$this->flashMessage("Update request sent for record '$id'; new values: " . Nette\Utils\Json::encode($values), 'success' );
		// !$this->isAjax() && $this->redirect('this'); // intentionally not redirecting (it's in the grid call)
	}



	function deleteMany(array $primaries)
	{
		// $this->ndb->table('user')->where('id', $primaries)->delete();
		$this->flashMessage('Records deletion request : ' . Nette\Utils\Json::encode($primaries), 'success');
		// !$this->isAjax() && $this->redirect('this'); // intentionally not redirecting (it's in the grid call)
	}



	// === APP-RELATED STUFF & HELPERS ===============================================================

	function inject(Nette\Database\Connection $c, Nette\Caching\IStorage $s)
	{
		$this->ndb = $c;
		$this->cache = new Nette\Caching\Cache($s, __CLASS__);
	}



	function loadState(array $params)
	{
		parent::loadState($params);

		if ($this->showQueries) {
			Helpers::initQueryLogging($this->ndb, $this->payload);
		}
	}



	protected function createTemplate($class = NULL)
	{
		Helpers::loadClientScripts($this->cache, __DIR__ . '/..');
		$this->invalidateControl('links');
		$this->invalidateControl('flashes');
		id ($template = parent::createTemplate($class))->showQueries = $this->showQueries;
		return $template
				->registerHelper('mtime', function ($f) { return $f . '?' . filemtime( __DIR__ . '/../' . $f ); })
				->setFile( __DIR__ . "/views/{$this->view}.latte" );
	}

}
