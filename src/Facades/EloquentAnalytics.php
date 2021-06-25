<?php

namespace Lester\EloquentGoogleAnalytics\Facades;

use Illuminate\Support\Facades\Facade;

class EloquentAnalytics extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'eloquent-analytics';
	}
}
