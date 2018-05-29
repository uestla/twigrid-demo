<?php

use Nette\Forms\Form;
use Nette\Forms\Container;
use Nette\Database\ResultSet;
use Nette\Database\Connection;
use Nette\Forms\Controls\TextInput;


/** Misc helpers for demo purposes */
class Helpers
{

	const SCRIPT_KEY = 'grid-script-';
	const DATE_REGEXP = '#^\s*(0[1-9]|[12][0-9]|3[01])\s*\.\s*(0?[1-9]|1[0-2])\s*\.\s*([0-9]{4})\s*$#';


	/**  @return array*/
	public static function getCountries()
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


	/**
	 * @param  Connection $connection
	 * @param  \stdClass $payload
	 * @return void
	 */
	public static function initQueryLogging(Connection $connection, $payload)
	{
		$payload->queries = [];
		$connection->onQuery[] = function (Connection $c, ResultSet $result) use ($payload) {
			self::logQuery($payload, $result->getPdoStatement()->queryString);
		};
	}


	/**
	 * @param  \stdClass $payload
	 * @param  string $sql
	 * @return void
	 */
	public static function logQuery($payload, $sql)
	{
		$payload->queries[] = dibi::dump($sql, TRUE);
	}


	/**
	 * @param  Container $container
	 * @param  string $name
	 * @return TextInput
	 */
	public static function addDateInput(Container $container, $name)
	{
		$control = $container->addText($name);
		$control->addCondition( Form::FILLED )->addRule( function ($control) {
			return self::parseDate($control->value) !== FALSE;
		}, 'Datum prosím zadávejte ve formátu "D.M.RRRR".' );

		return $control;
	}


	/**
	 * @param  string $s
	 * @return DateTime|FALSE
	 */
	public static function parseDate($s)
	{
		try {
			if (!($m = Nette\Utils\Strings::match($s, static::DATE_REGEXP))) {
				return FALSE;
			}

			return new DateTime("{$m[3]}-{$m[2]}-{$m[1]}");

		} catch (Exception $e) {}

		return FALSE;
	}

}
