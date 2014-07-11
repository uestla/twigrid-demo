<?php


class RowActionGrid extends BaseGrid
{

	/** @return void */
	protected function build()
	{
		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name');
		$this->addColumn('surname', 'Surname');
		$this->addColumn('country_code', 'Country');
		$this->addColumn('birthday', 'Birthdate');

		$this->addRowAction('download', 'Download', $this->downloadItem)
			->setProtected(FALSE); // turns of the CSRF protection (not necessary here)

		$this->addRowAction('delete', 'Delete', $this->deleteItem)
			->setConfirmation('Do you really want to delete this item?');

		$this->setDataLoader($this->dataLoader);
	}


	/**
	 * @param  RowActionGrid $grid
	 * @param  array $columns
	 * @param  array $filters
	 * @param  array $order
	 * @return Nette\Database\Table\Selection
	 */
	function dataLoader(RowActionGrid $grid, array $columns, array $filters, array $order)
	{
		return $this->database->table('user')
				->select(implode(', ', $columns))
				->limit(12);
	}


	/**
	 * @param  Nette\Database\Table\ActiveRow $record
	 * @return void
	 */
	function downloadItem(Nette\Database\Table\ActiveRow $record)
	{
		$this->flashMessage("[DEMO] Downloading item '{$record->id}'...", 'success');
	}


	/**
	 * @param  Nette\Database\Table\ActiveRow $record
	 * @return void
	 */
	function deleteItem(Nette\Database\Table\ActiveRow $record)
	{
		$this->flashMessage("[DEMO] Deleting item '{$record->id}'...", 'success');
	}

}
