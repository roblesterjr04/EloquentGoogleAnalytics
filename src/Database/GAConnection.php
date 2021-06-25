<?php

namespace Lester\EloquentGoogleAnalytics\Database;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\MySqlBuilder;
use Illuminate\Database\Query\Processors\MySqlProcessor;
use Illuminate\Database\Query\Grammars\MySqlGrammar as QueryGrammar;
use Illuminate\Database\Schema\Grammars\MySqlGrammar as SchemaGrammar;
use Omniphx\Forrest\Exceptions\MissingResourceException;
use Lester\EloquentGoogleAnalytics\Facades\EloquentAnalytics;
use Spatie\Analytics\AnalyticsFacade;
use Spatie\Analytics\Period;
use Closure;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GAConnection extends Connection
{
	/**
	 * {@inheritDoc}
	 */
	public function select($query, $bindings = [], $useReadPdo = true)
	{
		return $this->run($query, $bindings, function($query, $bindings) {
			if ($this->pretending()) {
				return [];
			}

			$statement = $this->prepare($query, $bindings);
			
			$keys = [
				'ga:date',
				'ga:pagePath'
			];
			/** @scrutinizer ignore-call */
			$result = EloquentAnalytics::performQuery($statement['period'], $statement['metrics'], [
				'dimensions' => implode(',', $keys),
			]);
			
			$keys[] = $statement['metrics'];
			$keys = array_map(function($key) {
				return str_replace('ga:', '', $key);
			}, $keys);
			
			$records = array_map(function($row) use ($keys) {
				return array_combine($keys, $row);
			}, $result->rows);
			
			while (isset($result['nextRecordsUrl'])) {
				$result = SObjects::next($result['nextRecordsUrl']);
				if (isset($result['records'])) {
					$records = \array_merge($records, $result['records']);
				}
			}

			return $records;
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function cursor($query, $bindings = [], $useReadPdo = true)
	{
		$result = $this->run($query, $bindings, function($query, $bindings) {
			if ($this->pretending()) {
				return [];
			}

			$statement = $this->prepare($query, $bindings);

			/** @scrutinizer ignore-call */
			return SObjects::query($statement);
		});

		while (true) {
			foreach ($result['records'] as $record) {
				yield $record;
			}
			if (!isset($result['nextRecordsUrl'])) {
				break;
			}
			$result = SObjects::next($result['nextRecordsUrl']);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function run($query, $bindings, Closure $callback)
	{
		$start = microtime(true);

		try {
			$result = $this->runQueryCallback($query, $bindings, $callback);
		} catch (QueryException $e) {
			$result = $this->handleQueryException(
				$e, $query, $bindings, $callback
			);
		}
		// Once we have run the query we will calculate the time that it took to run and
		// then log the query, bindings, and execution time so we will report them on
		// the event that the developer needs them. We'll log time in milliseconds.
		$this->logQuery(
			$query, $bindings, $this->getElapsedTime($start)
		);
		return $result;
	}

	private function prepare($query, $bindings)
	{
		$queryArray = [];
		$tableMatches = [];
		
		$startDate = null;
		$endDate = null;
		preg_match('/from `([a-z:]+)`/', $query, $tableMatches);
				
		foreach ($bindings as $binding) {
			if ($binding instanceof \Carbon\Carbon) {
				if ($startDate) $endDate = $binding;
				else $startDate = $binding;
			}
		}
		
		$queryArray['period'] = Period::create($startDate ?: today()->subWeek(), $endDate ?: today());
		$queryArray['metrics'] = $tableMatches[1];
		
		return $queryArray;
	}

}
