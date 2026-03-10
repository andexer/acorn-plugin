<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class LivewireServiceProvider extends ServiceProvider
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
		$pluginPath = base_path(); // Plugin directory (where vendor/ lives)
		$pluginUrl = plugins_url('', base_path('acorn-plugin.php'));

		// Forzar HTTPS si el sitio corre en SSL
		if (function_exists('is_ssl') && is_ssl()) {
			$pluginUrl = set_url_scheme($pluginUrl, 'https');
		}

		$this->publishLivewireAssets($pluginPath);
		$this->configureLivewireAssetUrl($pluginUrl);
		$this->publishFluxAssets($pluginPath);
		$this->overrideFluxScriptsDirective($pluginUrl, $pluginPath);
		$this->configureVite($pluginUrl, $pluginPath);
	}

	/**
	 * Copy Livewire JS assets to the plugin's public/vendor/livewire/ directory.
	 */
	private function publishLivewireAssets(string $pluginPath): void
	{
		$source = $pluginPath . '/vendor/livewire/livewire/dist';
		$dest = $pluginPath . '/public/vendor/livewire';

		if (! is_dir($source)) {
			return;
		}

		if (! is_dir($dest)) {
			@mkdir($dest, 0755, true);
		}

		$files = [
			'livewire.js',
			'livewire.min.js',
			'livewire.csp.js',
			'livewire.csp.min.js',
			'livewire.esm.js',
			'livewire.min.js.map',
			'livewire.csp.min.js.map',
			'livewire.esm.js.map',
			'livewire.csp.esm.js',
			'livewire.csp.esm.js.map',
			'manifest.json',
		];

		foreach ($files as $file) {
			$src = $source . '/' . $file;
			$dst = $dest . '/' . $file;

			if (file_exists($src) && (! file_exists($dst) || filemtime($src) > filemtime($dst))) {
				@copy($src, $dst);
			}
		}
	}

	/**
	 * Set Livewire's asset_url to point to the plugin's public directory.
	 */
	private function configureLivewireAssetUrl(string $pluginUrl): void
	{
		$debug = config('app.debug');
		$isCsp = config('livewire.csp_safe', false);

		if ($debug) {
			$file = $isCsp ? 'livewire.csp.js' : 'livewire.js';
		} else {
			$file = $isCsp ? 'livewire.csp.min.js' : 'livewire.min.js';
		}

		config()->set('livewire.asset_url', $pluginUrl . '/public/vendor/livewire/' . $file);
	}

	/**
	 * Copy Flux JS assets to the plugin's public/flux/ directory.
	 */
	private function publishFluxAssets(string $pluginPath): void
	{
		$fluxDist = $pluginPath . '/vendor/livewire/flux/dist';
		$fluxPublic = $pluginPath . '/public/flux';

		if (! is_dir($fluxDist)) {
			return;
		}

		if (! is_dir($fluxPublic)) {
			@mkdir($fluxPublic, 0755, true);
		}
		$files = ['flux-lite.min.js', 'manifest.json'];
		// Also copy pro files if available
		$fluxProDist = $pluginPath . '/vendor/livewire/flux-pro/dist';
		if (is_dir($fluxProDist)) {
			$files = array_merge($files, ['flux.js', 'flux.min.js']);
			$fluxDist = $fluxProDist;
		}

		foreach ($files as $file) {
			$source = $fluxDist . '/' . $file;
			$dest = $fluxPublic . '/' . $file;

			if (! file_exists($source)) {
				$source = $pluginPath . '/vendor/livewire/flux/dist/' . $file;
			}

			if (file_exists($source) && (! file_exists($dest) || filemtime($source) > filemtime($dest))) {
				@copy($source, $dest);
			}
		}
	}

	/**
	 * Override @fluxScripts Blade directive to serve from plugin's public URL.
	 */
	private function overrideFluxScriptsDirective(string $pluginUrl, string $pluginPath): void
	{
		$fluxPublicUrl = $pluginUrl . '/public/flux';

		Blade::directive('fluxScripts', function ($expression) use ($fluxPublicUrl, $pluginPath) {
			return <<<PHP
            <?php
                app('livewire')->forceAssetInjection();

                \$__pluginPath = '{$pluginPath}';
                \$__fluxManifestPath = \$__pluginPath . '/vendor/livewire/flux/dist/manifest.json';
                \$__fluxProManifestPath = \$__pluginPath . '/vendor/livewire/flux-pro/dist/manifest.json';

                if (file_exists(\$__fluxProManifestPath)) {
                    \$__fluxManifest = json_decode(file_get_contents(\$__fluxProManifestPath), true);
                } else {
                    \$__fluxManifest = json_decode(file_get_contents(\$__fluxManifestPath), true);
                }

                \$__fluxVersionHash = \$__fluxManifest['/flux.js'] ?? '';
                \$__fluxBaseUrl = '{$fluxPublicUrl}';

                if (config('app.debug')) {
                    \$__fluxFile = file_exists(\$__pluginPath . '/vendor/livewire/flux-pro/dist/flux.js')
                        ? 'flux.js'
                        : 'flux-lite.min.js';
                } else {
                    \$__fluxFile = file_exists(\$__pluginPath . '/vendor/livewire/flux-pro/dist/flux.min.js')
                        ? 'flux.min.js'
                        : 'flux-lite.min.js';
                }

                echo '<script src="' . \$__fluxBaseUrl . '/' . \$__fluxFile . '?id=' . \$__fluxVersionHash . '" defer data-navigate-once></script>';
            ?>
            PHP;
		});
	}

	/**
	 * Configure Vite to use the plugin's public directory by overriding the singleton.
	 */
	private function configureVite(string $pluginUrl, string $pluginPath): void
	{
		$this->app->singleton('assets.vite', function () use ($pluginUrl, $pluginPath) {
			return new class($this->app) extends \Roots\Acorn\Assets\Vite {
				protected $pluginUrl;
				protected $pluginPath;

				public function __construct($app)
				{
					// We need to find the plugin paths
					$this->pluginPath = base_path();
					$this->pluginUrl = plugins_url('', base_path('acorn-plugin.php'));
				}

				protected function publicPath($path)
				{
					return $this->pluginPath . '/public/' . ltrim($path, '/');
				}

				protected function manifestPath($buildDirectory)
				{
					return $this->publicPath($buildDirectory . '/' . $this->manifestFilename);
				}

				protected function assetPath($path, $secure = null)
				{
					return $this->pluginUrl . '/public/' . ltrim($path, '/');
				}
			};
		});

		$this->app->alias('assets.vite', \Illuminate\Foundation\Vite::class);
	}
}
