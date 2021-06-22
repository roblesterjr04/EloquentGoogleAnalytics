<?php

namespace Lester\EloquentGoogleAnalytics;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model as EloquentModel;
//use Lester\EloquentGoogleAnalytics\Database\SOQLBuilder as Builder;
//use Lester\EloquentGoogleAnalytics\Database\SOQLHasMany as HasMany;
//use Lester\EloquentGoogleAnalytics\Database\SOQLHasOne as HasOne;
//use Lester\EloquentGoogleAnalytics\Facades\Analytics;

abstract class Model extends EloquentModel
{
	
	public function save(array $args = [])
	{
		throw(new \Exception("Google analytics is Read Only."));
	}

}
