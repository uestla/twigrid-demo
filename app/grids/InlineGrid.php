<?php


class InlineGrid extends BaseGrid
{

	/** @return void */
	protected function build()
	{
		$this->setTemplateFile(__DIR__ . '/@inline.latte');

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name');
		$this->addColumn('surname', 'Surname');
		$this->addColumn('biography', 'Biography');
		$this->addColumn('country_code', 'Country');

		$me = $this;
		$this->setInlineEditing($this->inlineEditFactory, function ($id, Nette\Utils\ArrayHash $values) use ($me) {
			$me->flashMessage("[DEMO] Updating item '$id' with values " . Nette\Utils\Json::encode($values), 'success');
		});

		$this->setDataLoader($this->dataLoader);
	}


	/**
	 * @param  Nette\Database\Table\ActiveRow $record
	 * @return Nette\Forms\Container
	 */
	function inlineEditFactory(Nette\Database\Table\ActiveRow $record)
	{
		$c = new Nette\Forms\Container;

		$c->addText('firstname')->setRequired();
		$c->addText('surname')->setRequired();
		$c->addTextarea('biography')->setRequired()->setAttribute('rows', 7);

		$c->setDefaults($record->toArray());

		return $c;
	}


	/**
	 * @param  InlineGrid $grid
	 * @param  array $columns
	 * @param  array $filters
	 * @param  array $order
	 * @return Nette\Database\Table\Selection
	 */
	function dataLoader(InlineGrid $grid, array $columns, array $filters, array $order)
	{
		return $this->database->table('user')
			->select(implode(', ', $columns))
			->limit(12);
	}

}
