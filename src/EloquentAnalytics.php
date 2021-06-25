<?php

namespace Lester\EloquentGoogleAnalytics;

use Spatie\Analytics\AnalyticsFacade;

class EloquentAnalytics
{
	public function __call($name, $arguments)
	{
		return AnalyticsFacade::$name(...$arguments);
	}
}
