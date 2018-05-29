<?php

use Nette\Utils\Json;
use Nette\Forms\Container;
use Nette\Database\Table\ActiveRow;


class InlineGrid extends BaseGrid
{

	/** @return void */
	protected function build()
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


	/**
	 * @param  Container $container
	 * @param  ActiveRow $record
	 * @return void
	 */
	public function inlineEditFactory(Container $container, ActiveRow $record)
	{
		$container->addText('firstname')->setRequired();
		$container->addText('surname')->setRequired();
		$container->addTextarea('biography')->setRequired()->setAttribute('rows', 7);

		$container->setDefaults($record->toArray());
	}


	/**
	 * @param  array $filters
	 * @param  array $order
	 * @return Nette\Database\Table\Selection
	 */
	public function dataLoader(array $filters, array $order)
	{
		return $this->database->table('user')
			->limit(12);
	}

}
