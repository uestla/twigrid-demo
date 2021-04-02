<?php

declare(strict_types = 1);

use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;


class RowActionGrid extends BaseGrid
{

	protected function build(): void
	{
		parent::build();

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name');
		$this->addColumn('surname', 'Surname');
		$this->addColumn('country_code', 'Country');
		$this->addColumn('birthday', 'Birthdate');

		$this->addRowAction('download', 'Download', [$this, 'downloadItem'])
			->setProtected(false); // turns off the CSRF protection which is not necessary here

		$this->addRowAction('delete', 'Delete', [$this, 'deleteItem'])
			->setConfirmation('Do you really want to delete this item?');

		$this->setDataLoader([$this, 'dataLoader']);
	}


	/**
	 * @param  array<string, mixed> $filters
	 * @param  array<string, bool> $order
	 */
	public function dataLoader(array $filters, array $order): Selection
	{
		return $this->database->table('user')
				->limit(12);
	}


	public function downloadItem(ActiveRow $record): void
	{
		$this->flashMessage("[DEMO] Downloading item '{$record->id}'...", 'success');
	}


	public function deleteItem(ActiveRow $record): void
	{
		$this->flashMessage("[DEMO] Deleting item '{$record->id}'...", 'success');
	}

}
