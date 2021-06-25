<?php

namespace Lester\EloquentGoogleAnalytics;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Lester\EloquentGoogleAnalytics\Database\GABuilder as Builder;
//use Lester\EloquentGoogleAnalytics\Database\SOQLHasMany as HasMany;
//use Lester\EloquentGoogleAnalytics\Database\SOQLHasOne as HasOne;
//use Lester\EloquentGoogleAnalytics\Facades\Analytics;

abstract class Model extends EloquentModel
{
	protected $guarded = [];
	
	public function save(array $args = [])
	{
		throw(new \Exception("Google analytics is Read Only."));
	}
	
	/**
	 * Create a new Eloquent query builder for the model.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public function newEloquentBuilder($query)
	{
		/** @scrutinizer ignore-call */
		return new Builder($query);
	}

}
