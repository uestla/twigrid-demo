<?php

declare(strict_types = 1);

use Nette\Database\Explorer;
use Nette\Application\UI\Template;
use Nette\Application\UI\Presenter;


final class ExamplePresenter extends Presenter
{

	/** @persistent */
	public bool $showQueries = false;

	private Explorer $database;
	private SortingGrid $sortingGrid;
	private FilterGrid $filterGrid;
	private RowActionGrid $rowActionGrid;
	private GroupActionGrid $groupActionGrid;
	private InlineGrid $inlineGrid;
	private PaginationGrid $paginationGrid;
	private FullGrid $fullGrid;


	public function __construct(
		Explorer $database,
		SortingGrid $sortingGrid,
		FilterGrid $filterGrid,
		RowActionGrid $rowActionGrid,
		GroupActionGrid $groupActionGrid,
		InlineGrid $inlineGrid,
		PaginationGrid $paginationGrid,
		FullGrid $fullGrid

	) {
		parent::__construct();

		$this->database = $database;
		$this->fullGrid = $fullGrid;
		$this->filterGrid = $filterGrid;
		$this->inlineGrid = $inlineGrid;
		$this->sortingGrid = $sortingGrid;
		$this->rowActionGrid = $rowActionGrid;
		$this->paginationGrid = $paginationGrid;
		$this->groupActionGrid = $groupActionGrid;
	}


	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->sourceDir = __DIR__ . '/grids';
	}


	// === DATAGRIDS ==================================================================

	protected function createComponentSortingGrid(): SortingGrid
	{
		return $this->sortingGrid;
	}


	protected function createComponentFilterGrid(): FilterGrid
	{
		return $this->filterGrid;
	}


	protected function createComponentRowActionGrid(): RowActionGrid
	{
		return $this->rowActionGrid;
	}


	protected function createComponentGroupActionGrid(): GroupActionGrid
	{
		return $this->groupActionGrid;
	}


	protected function createComponentInlineGrid(): InlineGrid
	{
		return $this->inlineGrid;
	}


	protected function createComponentPaginationGrid(): PaginationGrid
	{
		return $this->paginationGrid;
	}


	protected function createComponentFullGrid(): FullGrid
	{
		return $this->fullGrid;
	}


	// === APP-RELATED STUFF & HELPERS ===============================================================

	/** @param  mixed[] $params */
	public function loadState(array $params): void
	{
		parent::loadState($params);

		if ($this->showQueries) {
			Helpers::initQueryLogging($this->database->getConnection(), $this->payload);
		}
	}


	protected function createTemplate(?string $class = null): Template
	{
		$this->redrawControl('links');
		$this->redrawControl('flashes');

		/** @var \Nette\Bridges\ApplicationLatte\Template $template */
		$template = parent::createTemplate($class);

		$template->showQueries = $this->showQueries;

		$template->getLatte()->addFilter('mtime', function ($f): string {
			return $f . '?' . filemtime(__DIR__ . '/../' . $f);
		});

		return $template->setFile(__DIR__ . "/views/{$this->view}.latte");
	}

}
