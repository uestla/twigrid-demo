<?php

declare(strict_types = 1);

use Nette\Utils\Json;
use TwiGrid\DataGrid;
use Nette\Forms\Container;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;


final class InlineGrid extends DataGrid
{

	private Explorer $database;


	public function __construct(Explorer $database)
	{
		parent::__construct();

		$this->database = $database;
	}


	protected function build(): void
	{
		$this->setTemplateFile(__DIR__ . '/@inline.latte');

		$this->setPrimaryKey('id');
		$this->addColumn('firstname', 'Name');
		$this->addColumn('surname', 'Surname');
		$this->addColumn('biography', 'Biography');
		$this->addColumn('country_code', 'Country');

		$this->setInlineEditing(static function (Container $container, ActiveRow $user): void {
			$container->addText('firstname')->setRequired();
			$container->addText('surname')->setRequired();
			$container->addTextArea('biography')->setRequired()->setHtmlAttribute('rows', 7);

			$container->setDefaults($user->toArray());

		}, function ($id, array $values): void {
			$this->flashMessage("[DEMO] Updating item '$id' with values " . Json::encode($values), 'success');
		});

		$this->setDataLoader(function (array $filters, array $order): Selection {
			return $this->database->table('user')
				->limit(12);
		});

		$this->setRecordVariable('user');
	}

}
