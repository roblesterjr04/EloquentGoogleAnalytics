<?php

namespace Lester\EloquentGoogleAnalytics\Tests;

use Lester\EloquentGoogleAnalytics\ServiceProvider;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Lester\EloquentGoogleAnalytics\TestModel;

class EloquentGoogleAnalyticsTest extends TestCase
{
	public function testBootstrap()
	{
		$this->assertTrue(true);
	}
	
	public function testSaveError()
	{
		$model = new TestModel();
		
		try {
			$model->save();
		} catch (\Exception $e) {
			$this->assertTrue(true);
			return;
		}
		$this->assertTrue(false);
	}
}