<?php

namespace Lester\EloquentGoogleAnalytics;

use Illuminate\Support\Arr;
use Lester\EloquentSalesForce\Console\MakeModelCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	const CONFIG_PATH = __DIR__ . '/../config/eloquent_ga.php';

	public function boot()
	{

		/*$this->publishes([
			self::CONFIG_PATH => config_path('eloquent_sf.php'),
		], 'config');

		if ($this->app->runningInConsole()) {
			$this->commands([
				MakeModelCommand::class,
			]);
		}*/
	}

	public function register()
	{
		/*$this->mergeConfigFrom(
			self::CONFIG_PATH,
			'eloquent_sf'
		);

		config([
			'forrest' => config('eloquent_sf.forrest'),
		]);

		$this->app->register(
			'Omniphx\Forrest\Providers\Laravel\ForrestServiceProvider'
		);

		$this->app->bind('sobjects', function() {
			return new SObjects();
		});

		$loader = \Illuminate\Foundation\AliasLoader::getInstance();
		$loader->alias('Forrest', 'Omniphx\Forrest\Providers\Laravel\Facades\Forrest');
		$loader->alias('SObjects', 'Lester\EloquentSalesForce\Facades\SObjects');
		*/
	}

}
