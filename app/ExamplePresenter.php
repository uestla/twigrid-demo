<?php


class ExamplePresenter extends Nette\Application\UI\Presenter
{

	/** @persistent bool */
	public $showQueries = FALSE;

	/** @var \Nette\Database\Context @inject */
	public $database;

	/** @var Nette\Caching\IStorage @inject */
	public $storage;

	/** @var Nette\Caching\Cache */
	private $cache;


	/** @return void */
	protected function startup()
	{
		parent::startup();

		$this->cache = new Nette\Caching\Cache($this->storage, __CLASS__);
	}


	/** @return void */
	protected function beforeRender()
	{
		parent::beforeRender();

		$this->template->sourceDir = __DIR__ . '/grids';
	}


	// === DATAGRIDS ==================================================================

	/** @return SimpleGrid */
	protected function createComponentSortingGrid()
	{
		return $this->context->createService('sortingGrid');
	}


	/** @return FilterGrid */
	protected function createComponentFilterGrid()
	{
		return $this->context->createService('filterGrid');
	}


	/** @return RowActionGrid */
	protected function createComponentRowActionGrid()
	{
		return $this->context->createService('rowActionGrid');
	}


	/** @return GroupActionGrid */
	protected function createComponentGroupActionGrid()
	{
		return $this->context->createService('groupActionGrid');
	}


	/** @return InlineGrid */
	protected function createComponentInlineGrid()
	{
		return $this->context->createService('inlineGrid');
	}


	/** @return PaginationGrid */
	protected function createComponentPaginationGrid()
	{
		return $this->context->createService('paginationGrid');
	}


	/** @return FullGrid */
	protected function createComponentFullGrid()
	{
		return $this->context->createService('fullGrid');
	}


	// === APP-RELATED STUFF & HELPERS ===============================================================

	/**
	 * @param  array $params
	 * @return void
	 */
	public function loadState(array $params)
	{
		parent::loadState($params);

		if ($this->showQueries) {
			Helpers::initQueryLogging($this->database->getConnection(), $this->payload);
		}
	}


	/**
	 * @param  string $class
	 * @return \Nette\Templating\ITemplate
	 */
	protected function createTemplate($class = NULL)
	{
		$this->redrawControl('links');
		$this->redrawControl('flashes');

		$template = parent::createTemplate($class);
		$template->showQueries = $this->showQueries;

		$template->getLatte()->addFilter('mtime', function ($f) {
			return $f . '?' . filemtime(__DIR__ . '/../' . $f);
		});

		return $template->setFile(__DIR__ . "/views/{$this->view}.latte");
	}

}
