<?php

declare(strict_types = 1);

use TwiGrid\DataGrid;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;


final class RowActionGrid extends DataGrid
{

	private Explorer $database;


	public function __construct(Explorer $database)
	{
		parent::__construct();

		$this->database = $database;
	}


	protected function build(): void
	{
		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name');
		$this->addColumn('surname', 'Surname');
		$this->addColumn('country_code', 'Country');
		$this->addColumn('birthday', 'Birthdate');

		$this->setDataLoader(function (array $filters, array $order): Selection {
			return $this->database->table('user')
				->limit(12);
		});

		$this->setRecordVariable('user');

		$this->addRowAction('download', 'Download', function (ActiveRow $user): void {
				$this->flashMessage("[DEMO] Downloading item '{$user->id}'...", 'success');
			})
			->setProtected(false); // turns off the CSRF protection which is not necessary here

		$this->addRowAction('delete', 'Delete', function (ActiveRow $user): void {
				$this->flashMessage("[DEMO] Deleting item '{$user->id}'...", 'success');
			})
			->setConfirmation('Do you really want to delete this item?');
	}

}
