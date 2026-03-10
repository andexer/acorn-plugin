<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 */
	public function register(): void
	{
		//
	}

	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		// Comandos de consola
		if ($this->app->runningInConsole()) {
			$this->commands([
				\Illuminate\Foundation\Console\ModelMakeCommand::class,
			]);
		}

		// Registrar shortcodes inicializando los controladores
		$this->app->make(\App\Http\Controllers\HomeController::class);
	}
}
