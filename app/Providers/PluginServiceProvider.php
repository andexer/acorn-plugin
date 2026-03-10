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
		add_action('wp_head', function () {
			echo \Illuminate\Support\Facades\Blade::render(
				"@vite(['resources/css/app.css', 'resources/js/app.js'])\n@livewireStyles\n@fluxAppearance"
			);
		});

		add_action('wp_footer', function () {
			echo \Illuminate\Support\Facades\Blade::render(
				"@livewireScripts\n@fluxScripts"
			);
		});
		// Registrar shortcodes inicializando los controladores
		$this->app->make(\App\Http\Controllers\HomeController::class);
	}
}
