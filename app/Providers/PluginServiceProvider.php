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
		// Forzar HTTPS si WordPress está en SSL (soluciona el error Mixed Content)
		if (function_exists('is_ssl') && is_ssl()) {
			\Illuminate\Support\Facades\URL::forceScheme('https');
		}

		// Prioridad 10 para CSS
		add_action('wp_head', function () {
			echo \Illuminate\Support\Facades\Blade::render(
				"@vite(['resources/css/app.css'])\n@livewireStyles"
			);
		});

		add_action('wp_footer', function () {
			echo \Illuminate\Support\Facades\Blade::render(
				"@vite(['resources/js/app.js'])\n@livewireScripts\n@fluxScripts"
			);
		});
		// Registrar shortcodes inicializando los controladores
		$this->app->make(\App\Http\Controllers\HomeController::class);
	}
}
