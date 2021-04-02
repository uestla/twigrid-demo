<?php

declare(strict_types = 1);

use Nette\Forms\Form;
use Nette\Utils\Strings;
use Nette\Forms\Container;
use Nette\Database\ResultSet;
use Nette\Database\Connection;
use Nette\Forms\Controls\TextInput;


/** Misc helpers for demo purposes */
class Helpers
{

	const SCRIPT_KEY = 'grid-script-';
	const DATE_REGEXP = '#^\s*(0[1-9]|[12][0-9]|3[01])\s*\.\s*(0?[1-9]|1[0-2])\s*\.\s*([0-9]{4})\s*$#';


	/**  @return array<string, string> */
	public static function getCountries(): array
	{
		return [
			'au' => 'Australia',
			'at' => 'Austria',
			'be' => 'Belgium',
			'ca' => 'Canada',
			'ch' => 'Switzerland',
			'cz' => 'Czech Republic',
			'de' => 'Germany',
			'es' => 'Spain',
			'fi' => 'Finland',
			'fr' => 'France',
			'gb' => 'United Kingdom',
			'hu' => 'Hungary',
			'is' => 'Iceland',
			'it' => 'Italy',
			'pl' => 'Poland',
			'se' => 'Sweden',
			'us' => 'United States',
		];
	}


	public static function initQueryLogging(Connection $connection, \stdClass $payload): void
	{
		$payload->queries = [];
		$connection->onQuery[] = function (Connection $c, ResultSet $result) use ($payload) {
			self::logQuery($payload, $result->getPdoStatement()->queryString);
		};
	}


	public static function logQuery(\stdClass$payload, string $sql): void
	{
		$payload->queries[] = dibi::dump($sql, true);
	}


	public static function addDateInput(Container $container, string $name): TextInput
	{
		$control = $container->addText($name);
		$control->addCondition( Form::FILLED )->addRule( function ($control) {
			return self::parseDate($control->value) !== null;
		}, 'Datum prosím zadávejte ve formátu "D.M.RRRR".' );

		return $control;
	}


	public static function parseDate(string $s): ?\DateTime
	{
		try {
			if (!($m = Strings::match($s, static::DATE_REGEXP))) {
				return null;
			}

			return new DateTime("{$m[3]}-{$m[2]}-{$m[1]}");

		} catch (Exception $e) {}

		return null;
	}

}
