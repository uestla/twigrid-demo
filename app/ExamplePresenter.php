<?php

use Nette\Forms\Form;


/** @persistent(dataGrid) */
class ExamplePresenter extends Nette\Application\UI\Presenter
{
	/** @persistent bool */
	public $showQueries = FALSE;

	/** @var Nette\Database\Connection */
	protected $ndb;

	/** @var Nette\Caching\Cache */
	protected $cache;

	const SCRIPT_KEY = 'grid-script-';



	// === DATAGRID DEFINITION ==================================================================

	protected function createComponentDataGrid()
	{
		$grid = $this->context->createDataGrid();
		$grid->setTemplateFile( __DIR__ . '/user-grid.latte' );

		$grid->addColumn('firstname', 'Jméno')->setSortable();
		$grid->addColumn('surname', 'Příjmení')->setSortable();
		$grid->addColumn('country', 'Země');
		$grid->addColumn('birthday', 'Datum narození')->setSortable();
		$grid->addColumn('kilograms', 'Váha (kg)')->setSortable();

		$grid->setPrimaryKey( $this->ndb->table('user')->primary );
		$grid->setFilterContainerFactory( $this->createFilterContainer );
		$grid->setDataLoader( $this->dataLoader );
		$grid->setTimelineBehavior();

		$grid->setInlineEditing($this->createInlineEditContainer, $this->processInlineEditForm);
		$grid->addRowAction('delete', 'Smazat', $this->deleteRecord, 'Opravdu chcete smazat tento záznam?');

		$minmax = $this->loadMinMaxBirthday();
		$grid->setDefaultFilters(array(
			'birthday' => array(
				'min' => id( new DateTime($minmax['min']) )->format('d. m. Y'),
				'max' => id( new DateTime($minmax['max']) )->format('d. m. Y'),
			),
		));

		return $grid;
	}



	function createFilterContainer()
	{
		$container = new Nette\Forms\Container;

		$container->addSelect('gender', 'Pohlaví', array(
			'male' => 'Muž',
			'female' => 'Žena',
		))->setPrompt('---');

		$container->addText('firstname');
		$container->addText('surname');

		$birthday = $container->addContainer('birthday');
		$min = $this->addDateInput( $birthday, 'min' );
		$max = $this->addDateInput( $birthday, 'max' );

		$parser = $this->parseDate;
		$min->addCondition( Form::FILLED )->addRule( function () use ($min, $max, $parser) {
			return !$max->filled || (($minDt = $parser( $min->value )) !== FALSE && ($maxDt = $parser( $max->value )) !== FALSE && $minDt <= $maxDt);
		}, 'Minimální datum nesmí následovat po maximálním.' );

		$container->addSelect( 'country', 'Země', $this->loadCountries() )
				->setPrompt('---');

		$container->addText('kilograms')->addCondition( Form::FILLED )->addRule( Form::FLOAT );

		return $container;
	}



	function createInlineEditContainer($record)
	{
		$container = new Nette\Forms\Container;
		$container->addText('firstname')->setRequired('Zadejte prosím jméno.');
		$container->addText('surname')->setRequired('Zadejte prosím příjmení.');
		$container->addSelect( 'country', 'Země', $this->loadCountries() )->setRequired('Zvolte zemi původu.')
				->setDefaultValue( $record->country_code );
		$this->addDateInput($container, 'birthday')->setRequired('Zadejte datum narození.');
		$container->addText('kilograms')->addRule( Form::FLOAT, 'Váhu zadejte jako číslo.' );
		$defaults = $record->toArray();
		$defaults['birthday'] = id( new DateTime($defaults['birthday']) )->format('d. m. Y');
		return $container->setDefaults( $defaults );
	}



	protected function loadCountries()
	{
		$key = __METHOD__;
		$countries = $this->cache->load($key);
		return $countries !== NULL ? $countries : $this->cache->save( $key, $this->ndb->table('country')
				->select( '('
					. $this->ndb->table('user')
						->select('id')
						->where('country_code = code')
						->limit(1)
						->getSql()
				. ') AS is_used, code, title')
				->where('is_used IS NOT NULL')
				->fetchPairs('code', 'title') );
	}



	function loadMinMaxBirthday()
	{
		$key = __METHOD__;
		$countries = $this->cache->load($key);
		return $countries !== NULL ? $countries : $this->cache->save( $key, $this->ndb->table('user')
				->select('MIN(birthday) AS min, MAX(birthday) AS max')
				->fetch()->toArray() );
	}



	function dataLoader(TwiGrid\DataGrid $grid, array $columns, array $orderBy, array $filters, $page)
	{
		// selection factory
		$users = $this->ndb->table('user');

		// columns
		unset($columns['country']);
		$columns[] = 'country_code';
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

			} elseif ($column === 'country') {
				$conds['country_code'] = $value;

			} elseif ($column === 'birthday') {
				isset($value['min']) && $conds["$column >= ?"] = $this->parseDate( $value['min'] )->format('Y-m-d');
				isset($value['max']) && $conds["$column <= ?"] = $this->parseDate( $value['max'] )->format('Y-m-d');

			} elseif ($column === 'kilograms') {
				$conds["$column <= ?"] = $value;

			} elseif ($column === 'firstname' || $column === 'surname') {
				$conds["$column LIKE ?"] = "$value%";

			} else {
				$conds["$column LIKE ?"] = "%$value%";
			}
		}

		$max = 42;
		$grid->setCountAll( min($max, $users->where($conds)->count('*')) );
		return $users->limit( min($max, $page * 12) );
	}



	// === DATA MANIPULATIONS ===============================================================

	function deleteRecord($id)
	{
		$this->flashMessage( "Požadavek na smazání záznamu s ID '$id'.", 'warning' );
		!$this->isAjax() && $this->redirect('this');
	}



	function processInlineEditForm($id, array $values)
	{
		$this->flashMessage( "Požadavek na změnu záznamu s ID '$id'; nové hodnoty: " . Nette\Utils\Json::encode($values), 'success' );
		!$this->isAjax() && $this->redirect('this');
	}



	// === HELPERS & APP-RELATED STUFF ===============================================================

	function inject(Nette\Database\Connection $c, Nette\Caching\IStorage $s)
	{
		$this->ndb = $c;
		$this->cache = new Nette\Caching\Cache($s, __CLASS__);
	}



	function loadState(array $params)
	{
		parent::loadState($params);

		if ($this->showQueries) {
			$me = $this;
			$this->payload->queries = array();
			$this->ndb->onQuery[] = function ($s) use ($me) { $me->logQuery( $s->queryString ); };
		}
	}



	function logQuery($sql)
	{
		$this->payload->queries[] = dibi::dump( $sql, TRUE );
	}



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



	protected function createTemplate($class = NULL)
	{
		$this->loadClientScripts();
		$this->invalidateControl('links');
		$this->invalidateControl('flashes');
		id ($template = parent::createTemplate($class))->showQueries = $this->showQueries;
		return $template
				->registerHelper('mtime', function ($f) { return $f . '?' . filemtime( __DIR__ . '/../' . $f ); })
				->setFile( __DIR__ . "/views/{$this->view}.latte" );
	}



	protected function addDateInput(Nette\Forms\Container $container, $name)
	{
		$control = $container->addText($name);
		$parser = $this->parseDate;
		$control->addCondition( Form::FILLED )->addRule( function ($control) use ($parser) {
			return $parser( $control->value ) !== FALSE;
		}, 'Datum prosím zadávejte ve formátu "D.M.RRRR".' );

		return $control;
	}



	function parseDate($s)
	{
		try {
			if (!($m = Nette\Utils\Strings::match($s, '#^\s*(0[1-9]|[12][0-9]|3[01])\s*\.\s*(0?[1-9]|1[0-2])\s*\.\s*([0-9]{4})\s*$#'))) {
				return FALSE;
			}

			return new DateTime("{$m[3]}-{$m[2]}-{$m[1]}");

		} catch (Exception $e) {}

		return FALSE;
	}
}
