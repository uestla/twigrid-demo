<?php


class InlineGrid extends TwiGrid\DataGrid
{

	/** @var Nette\Database\Context @inject */
	public $database;



	protected function build()
	{
		$this->setTemplateFile(__DIR__ . '/@inline.latte');

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name');
		$this->addColumn('surname', 'Surname');
		$this->addColumn('biography', 'Biography');
		$this->addColumn('country_code', 'Country');

		$me = $this;
		$this->setInlineEditing($this->inlineEditFactory, function ($id, array $values) use ($me) {
			$me->flashMessage("[DEMO] Updating item '$id' with values " . Nette\Utils\Json::encode($values), 'success');
		});

		$this->setDataLoader($this->dataLoader);
	}



	function inlineEditFactory(Nette\Database\Table\ActiveRow $record)
	{
		$c = new Nette\Forms\Container;

		$c->addText('firstname')->setRequired();
		$c->addText('surname')->setRequired();
		$c->addTextarea('biography')->setRequired()->setAttribute('rows', 7);

		$c->setDefaults($record->toArray());

		return $c;
	}



	function dataLoader(InlineGrid $grid, array $columns, array $filters, array $order)
	{
		return $this->database->table('user')
			->select(implode(', ', $columns))
			->limit(12);
	}

}
