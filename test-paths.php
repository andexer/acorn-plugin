<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = Roots\Acorn\Application::configure(__DIR__)->boot();

echo "Plugin Path (public): " . get_plugin_path('public') . "\n";
echo "Plugin URI (public): " . get_plugin_uri('public') . "\n";
echo "Vite Manifest Path: " . get_plugin_path('public/build/manifest.json') . "\n";
echo "File Exists (Manifest): " . (file_exists(get_plugin_path('public/build/manifest.json')) ? 'YES' : 'NO') . "\n";
