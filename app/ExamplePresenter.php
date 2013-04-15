<?php


class ExamplePresenter extends Nette\Application\UI\Presenter
{

	/** @persistent bool */
	public $showQueries = FALSE;

	/** @var Nette\Database\Connection */
	protected $ndb;

	/** @var Nette\Caching\Cache */
	protected $cache;



	/** @return void */
	protected function beforeRender()
	{
		parent::beforeRender();

		$this->template->sourceDir = __DIR__ . '/grids';
	}



	// === DATAGRIDS ==================================================================

	/** @return SimpleGrid */
	protected function createComponentSimpleGrid()
	{
		$cc = $this->context->createSimpleGrid();
		return $cc;
	}



	/** @return FilterGrid */
	protected function createComponentFilterGrid()
	{
		$cc = $this->context->createFilterGrid();
		return $cc;
	}



	/** @return RowActionGrid */
	protected function createComponentRowActionGrid()
	{
		$cc = $this->context->createRowActionGrid();
		return $cc;
	}



	/** @return GroupActionGrid */
	protected function createComponentGroupActionGrid()
	{
		$cc = $this->context->createGroupActionGrid();
		return $cc;
	}



	/** @return InlineGrid */
	protected function createComponentInlineGrid()
	{
		$cc = $this->context->createInlineGrid();
		return $cc;
	}



	/** @return PaginationGrid */
	protected function createComponentPaginationGrid()
	{
		$cc = $this->context->createPaginationGrid();
		return $cc;
	}



	/** @return FullGrid */
	protected function createComponentFullGrid()
	{
		$cc = $this->context->createFullGrid();
		return $cc;
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
		id($template = parent::createTemplate($class))->showQueries = $this->showQueries;
		return $template
				->registerHelper('mtime', function ($f) { return $f . '?' . filemtime(__DIR__ . '/../' . $f); })
				->setFile(__DIR__ . "/views/{$this->view}.latte");
	}

}
