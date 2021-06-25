<?php

namespace Lester\EloquentGoogleAnalytics\Database;

use Illuminate\Database\Eloquent\Builder as Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\Paginator;
use Lester\EloquentGoogleAnalytics\ServiceProvider;
use Lester\EloquentGoogleAnalytics\Facades\EloquentAnalytics;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class GABuilder extends Builder
{
	/**
	 * {@inheritDoc}
	 */
	public function __construct(QueryBuilder $query)
	{
		$query->connection = new GAConnection(null);
		$query->grammar = new GAGrammar();

		parent::__construct($query);
	}

	public function toSql()
	{
		$columns = implode(', ', $this->describe());
		$query = str_replace('*', $columns, parent::toSql());
		$query = str_replace('`', '', $query);
		$bindings = array_map(function($item) {
			try {
				if (strtotime($item) !== false && !$this->query->connection->isSalesForceId($item)) {
					return $item;
				}
			} catch (\Exception $e) {
				if (is_int($item) || is_float($item)) {
					return $item;
				} else {
					return "'$item'";
				}
			}
			return "'$item'";
		}, $this->getBindings());
		$prepared = Str::replaceArray('?', $bindings, $query);
		return $prepared;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getModels($columns = ['*'])
	{
		return parent::getModels($columns);
	}

	/**
	 * {@inheritDoc}
	 */
	public function cursor()
	{
		return parent::cursor();
	}

	/**
	 * {@inheritDoc}
	 */
	public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
	{
		$columns = $this->getSalesForceColumns($columns);

		$table = $this->model->getTable();

		/** @scrutinizer ignore-call */
		//$total = SObjects::query("SELECT COUNT() FROM $table")['totalSize'];
		$builder = $this->getQuery()->cloneWithout(
			['columns', 'orders', 'limit', 'offset']
		);
		$builder->aggregate = ['function' => 'count', 'columns' => ['Id']];
		$total = $builder->get()[0]['aggregate'];
		if ($total > 2000) { // SOQL OFFSET limit is 2000
			$total = 2000;
		}

		$page = $page ?: Paginator::resolveCurrentPage($pageName);
		$perPage = $perPage ?: $this->model->getPerPage();
		$results = $total
			? /** @scrutinizer ignore-call */ $this->forPage($page, $perPage)->get($columns)
			: $this->model->newCollection();
		return $this->paginator($results, $total, $perPage, $page, [
			'path' => Paginator::resolveCurrentPath(),
			'pageName' => $pageName,
		]);
	}

}
