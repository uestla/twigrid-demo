<?php

declare(strict_types = 1);

use Nette\Forms\Form;
use Nette\Utils\Strings;
use Nette\Forms\Container;
use Nette\Database\ResultSet;
use Nette\Database\Connection;
use Nette\Forms\Controls\TextInput;


final class Helpers
{

	private const DATE_REGEXP = '#^\s*(\d+)\s*\.\s*(\d+)\s*\.\s*(\d+)\s*$#';


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

		$connection->onQuery[] = static function (Connection $c, ResultSet $result) use ($payload): void {
			$stmt = $result->getPdoStatement();

			if ($stmt === null) {
				return ;
			}

			$payload->queries[] = dibi::dump($stmt->queryString, true);
		};
	}


	public static function addDateInput(Container $container, string $name): TextInput
	{
		$control = $container->addText($name);

		$control->addCondition(Form::FILLED)
			->addRule(static function ($control): bool {
				return self::parseDate($control->value) !== null;
			}, 'Please provide a date using format "DD. MM. YYYY".');

		return $control;
	}


	public static function parseDate(string $s): ?\DateTime
	{
		try {
			if (($m = Strings::match($s, self::DATE_REGEXP)) === null) {
				return null;
			}

			if (!checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
				return null;
			}

			return new \DateTime("$m[3]-$m[2]-$m[1] 00:00:00");

		} catch (\Exception $e) {}

		return null;
	}

}
