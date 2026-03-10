<?php

/*
Plugin Name: Acorn Plugin
Description: A simple plugin to demonstrate Acorn integration.
Version: 1.0.0
Author: Acorn Team
*/

if (!defined('WPINC')) {
	die;
}

if (file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
	require_once $composer;
}
if (file_exists(__DIR__ . '/.env')) {
	\Dotenv\Dotenv::createUnsafeImmutable(__DIR__)->safeLoad();
}

use Roots\Acorn\Application;
use App\Providers\PluginServiceProvider;
use App\Providers\LivewireServiceProvider;

if (! class_exists(Application::class)) {
	add_action('admin_notices', function (): void {
		printf(
			'<div class="notice notice-error"><p>%s <a href="https://roots.io/acorn/docs/installation/">%s</a></p></div>',
			__('Es necesario instalar Acorn para usar el plugin Acorn Plugin.', 'acorn-plugin'),
			__('Documentación de instalación', 'acorn-plugin')
		);
	});
	return;
}

add_action('plugins_loaded', function (): void {
	Application::configure()
		->withProviders(providers: [
			PluginServiceProvider::class,
			LivewireServiceProvider::class,
		])
		->withRouting(wordpress: true)
		->boot();
}, 0);
