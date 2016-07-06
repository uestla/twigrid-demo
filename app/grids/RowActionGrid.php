<?php


class RowActionGrid extends BaseGrid
{

	/** @return void */
	protected function build()
	{
		parent::build();

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name');
		$this->addColumn('surname', 'Surname');
		$this->addColumn('country_code', 'Country');
		$this->addColumn('birthday', 'Birthdate');

		$this->addRowAction('download', 'Download', [$this, 'downloadItem'])
			->setProtected(FALSE); // turns off the CSRF protection which is not necessary here

		$this->addRowAction('delete', 'Delete', [$this, 'deleteItem'])
			->setConfirmation('Do you really want to delete this item?');

		$this->setDataLoader([$this, 'dataLoader']);
	}


	/**
	 * @param  RowActionGrid $grid
	 * @param  array $filters
	 * @param  array $order
	 * @return Nette\Database\Table\Selection
	 */
	public function dataLoader(RowActionGrid $grid, array $filters, array $order)
	{
		return $this->database->table('user')
				->limit(12);
	}


	/**
	 * @param  Nette\Database\Table\ActiveRow $record
	 * @return void
	 */
	public function downloadItem(Nette\Database\Table\ActiveRow $record)
	{
		$this->flashMessage("[DEMO] Downloading item '{$record->id}'...", 'success');
	}


	/**
	 * @param  Nette\Database\Table\ActiveRow $record
	 * @return void
	 */
	public function deleteItem(Nette\Database\Table\ActiveRow $record)
	{
		$this->flashMessage("[DEMO] Deleting item '{$record->id}'...", 'success');
	}

}
