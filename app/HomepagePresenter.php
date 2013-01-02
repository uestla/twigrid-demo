<?php

use Nette\Forms\Form;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
	/** @var Nette\Database\Connection */
	protected $database;

	/** @var Nette\Caching\Cache */
	protected $cache;

	const SCRIPT_KEY = 'grid-script-';



	/**
	 * @param  Nette\Database\Connection
	 * @param  Nette\Caching\IStorage
	 * @return void
	 */
	function inject(Nette\Database\Connection $c, Nette\Caching\IStorage $s)
	{
		$this->database = $c;
		$this->cache = new Nette\Caching\Cache($s, __CLASS__);
	}



	/** @return void */
	protected function loadClientScripts()
	{
		foreach (array('js/twigrid.datagrid.js', 'css/twigrid.datagrid.css') as $file) {
			( ( $key = static::SCRIPT_KEY . $file ) && is_file( $dest = __DIR__ . '/../' . $file ) && $this->cache->load( $key ) ) || (
				copy($source = __DIR__ . '/../libs/TwiGrid/client-side/' . basename($file), $dest)
				&& $this->cache->save($key, TRUE, array(
					Nette\Caching\Cache::FILES => array($source),
				))
			);
		}
	}



	/**
	 * @param  string|NULL
	 * @return Nette\Templating\ITemplate
	 */
	protected function createTemplate($class = NULL)
	{
		$this->loadClientScripts();
		$this->invalidateControl('links');
		$this->invalidateControl('flashes');
		return parent::createTemplate( $class )->setFile( __DIR__ . '/template.latte' );
	}



	/** @return TwiGrid\DataGrid */
	protected function createComponentDataGrid()
	{
		$grid = $this->context->createDataGrid();
		$grid->setTemplateFile( __DIR__ . '/user-grid.latte' );

		$grid->addColumn('gender', 'Pohlaví');
		$grid->addColumn('firstname', 'Jméno')->setSortable();
		$grid->addColumn('surname', 'Příjmení')->setSortable();
		$grid->addColumn('emailaddress', 'E-mail')->setSortable();
		$grid->addColumn('birthday', 'Datum narození')->setSortable();
		$grid->addColumn('kilograms', 'Váha (kg)')->setSortable();
		$grid->addColumn('centimeters', 'Výška (cm)')->setSortable();

		$grid->setPrimaryKey( $this->database->table('user')->primary );
		$grid->setFilterContainerFactory( $this->createFilterContainer );
		$grid->setDataLoader( $this->dataLoader );

		$grid->addRowAction('edit', 'Upravit', $this->editRecord);
		$grid->addRowAction('delete', 'Smazat', $this->deleteRecord, 'Opravdu chcete smazat tento záznam?');
		$grid->addGroupAction('change', 'Změnit záznamy', $this->manipulateGroup, 'Opravdu chcete změnit vybrané položky?');

		return $grid;
	}



	/** @return Nette\Forms\Container */
	function createFilterContainer()
	{
		$container = new Nette\Forms\Container;

		$container->addSelect('gender', 'Pohlaví', array(
			'male' => 'Muž',
			'female' => 'Žena',
		))->setPrompt('---');

		$container->addText('firstname');
		$container->addText('surname');

		/* $birthday = $container->addContainer('birthday');
		$birthday->addText('min');
		$birthday->addText('max'); */

		$container->addText('kilograms')->addCondition( Form::FILLED )->addRule( Form::FLOAT );
		$container->addText('centimeters')->addCondition( Form::FILLED )->addRule( Form::INTEGER );

		return $container;
	}



	/**
	 * @param  array
	 * @param  array column => desc?
	 * @param  array
	 * @return Nette\Database\Table\Selection
	 */
	function dataLoader(array $columns, array $orderBy, array $filters)
	{
		// selection factory
		$users = $this->database->table('user');

		// columns
		$users->select( implode(', ', $columns) );

		// order result
		foreach ($orderBy as $column => $desc) {
			$users->order( $column . ($desc ? ' DESC' : '') );
		}

		// filter result
		$conds = array();
		foreach ($filters as $column => $value) {
			if ($column === 'gender') {
				$conds[ $column ] = $value;

			} elseif ($column === 'birthday') {
				isset($value['min']) && $conds["$column >= ?"] = $value['min'];
				isset($value['max']) && $conds["$column <= ?"] = $value['max'];

			} elseif ($column === 'centimeters') {
				$conds["$column <= ?"] = $value;

			} elseif ($column === 'kilograms') {
				$conds["$column <= ?"] = $value;

			} else {
				$conds["$column LIKE ?"] = "$value%";
			}
		}

		return $users->where($conds)->limit(16);
	}



	/**
	 * @param  int
	 * @return void
	 */
	function editRecord($id)
	{
		$this->flashMessage( "Požadavek na změnu záznamu s ID '$id'.", 'success' );
		!$this->isAjax() && $this->redirect('this');
	}



	/**
	 * @param  int
	 * @return void
	 */
	function deleteRecord($id)
	{
		$this->flashMessage( "Požadavek na smazání záznamu s ID '$id'.", 'warning' );
		!$this->isAjax() && $this->redirect('this');
	}



	/**
	 * @param  array
	 * @return void
	 */
	function manipulateGroup(array $primaries)
	{
		$this->flashMessage( "Požadavek na změnu záznamů s ID: " . Nette\Utils\Json::encode($primaries), 'success' );
		!$this->isAjax() && $this->redirect('this');
	}
}
