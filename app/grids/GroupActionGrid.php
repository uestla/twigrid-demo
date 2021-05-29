<?php

declare(strict_types = 1);

use Nette\Utils\Json;
use TwiGrid\DataGrid;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;


final class GroupActionGrid extends DataGrid
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

		$this->addGroupAction('export', 'Export', function (array $users): void {
			$IDs = [];
			foreach ($users as $user) {
				$IDs[] = $user->id;
			}

			$this->flashMessage('[DEMO] Exporting items ' . Json::encode($IDs), 'success');
		});

		$this->addGroupAction('delete', 'Delete', function (array $users): void {
				$ids = [];
				foreach ($users as $user) {
					$ids[] = $user->id;
				}

				$this->flashMessage('[DEMO] Deleting items ' . Json::encode($ids), 'success');
			})
			->setConfirmation('Do you really want to delete all chosen items?');
	}

}
