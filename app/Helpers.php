<?php

use Nette\Forms\Form;
use Nette\Caching\Cache;
use Nette\Forms\Container;
use Nette\Database\Statement;
use Nette\Database\Connection;


/**
 * Misc helpers for demo purposes
 */
class Helpers
{

	const SCRIPT_KEY = 'grid-script-';
	const DATE_REGEXP = '#^\s*(0[1-9]|[12][0-9]|3[01])\s*\.\s*(0?[1-9]|1[0-2])\s*\.\s*([0-9]{4})\s*$#';



	static function getCountries()
	{
		return array(
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
		);
	}



	static function initQueryLogging(Connection $connection, $payload)
	{
		$logger = callback(__CLASS__, 'logQuery');
		$payload->queries = array();
		$connection->onQuery[] = function (Statement $st) use ($logger, $payload) {
			$logger($payload, $st->queryString);
		};
	}



	static function logQuery($payload, $sql)
	{
		$payload->queries[] = dibi::dump($sql, TRUE);
	}



	static function loadClientScripts(Cache $cache, $baseDir)
	{
		foreach (array('js/twigrid.datagrid.js', 'css/twigrid.datagrid.css') as $file) {
			( ( $key = static::SCRIPT_KEY . $file ) && is_file( $dest = $baseDir . '/' . $file )
					&& $cache->load( $key ) ) || (
				copy($source = $baseDir . '/libs/TwiGrid/client-side/' . basename($file), $dest)
					&& $cache->save($key, TRUE, array(
						Cache::FILES => array($source),
					))
			);
		}
	}



	static function addDateInput(Container $container, $name)
	{
		$control = $container->addText($name);
		$parser = callback(__CLASS__, 'parseDate');
		$control->addCondition( Form::FILLED )->addRule( function ($control) use ($parser) {
			return $parser($control->value) !== FALSE;
		}, 'Datum prosím zadávejte ve formátu "D.M.RRRR".' );

		return $control;
	}



	static function parseDate($s)
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
