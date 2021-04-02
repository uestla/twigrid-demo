<?php

declare(strict_types = 1);

use Nette\Utils\Json;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;


class GroupActionGrid extends BaseGrid
{

	protected function build(): void
	{
		parent::build();

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name');
		$this->addColumn('surname', 'Surname');
		$this->addColumn('country_code', 'Country');
		$this->addColumn('birthday', 'Birthdate');

		$this->addGroupAction('export', 'Export', [$this, 'exportMany']);

		$this->addGroupAction('delete', 'Delete', [$this, 'deleteMany'])
			->setConfirmation('Do you really want to delete all chosen items?');

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


	/** @param  ActiveRow[] $records */
	public function exportMany(array $records): void
	{
		$ids = [];
		foreach ($records as $record) {
			$ids[] = $record->id;
		}

		$this->flashMessage('[DEMO] Exporting items ' . Json::encode($ids), 'success');
	}


	/** @param  ActiveRow[] $records */
	public function deleteMany(array $records): void
	{
		$ids = [];
		foreach ($records as $record) {
			$ids[] = $record->id;
		}

		$this->flashMessage('[DEMO] Deleting items ' . Json::encode($ids), 'success');
	}

}
