<?php

declare(strict_types = 1);

use Nette\Utils\Json;
use Nette\Forms\Container;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;


class InlineGrid extends BaseGrid
{

	protected function build(): void
	{
		parent::build();

		$this->setTemplateFile(__DIR__ . '/@inline.latte');

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name');
		$this->addColumn('surname', 'Surname');
		$this->addColumn('biography', 'Biography');
		$this->addColumn('country_code', 'Country');

		$this->setInlineEditing([$this, 'inlineEditFactory'], function ($id, array $values) {
			$this->flashMessage("[DEMO] Updating item '$id' with values " . Json::encode($values), 'success');
		});

		$this->setDataLoader([$this, 'dataLoader']);
	}


	public function inlineEditFactory(Container $container, ActiveRow $record): void
	{
		$container->addText('firstname')->setRequired();
		$container->addText('surname')->setRequired();
		$container->addTextarea('biography')->setRequired()->setAttribute('rows', 7);

		$container->setDefaults($record->toArray());
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

}
