<?php

/**
 * Get the plugin path.
 *
 * @param  string  $path
 * @return string
 */
if (!function_exists('get_plugin_path')) {
	function get_plugin_path($path = '')
	{
		return plugin_dir_path(dirname(__DIR__)) . ltrim($path, '/');
	}
}

/**
 * Get the plugin URI.
 *
 * @param  string  $path
 * @return string
 */
if (!function_exists('get_plugin_uri')) {
	function get_plugin_uri($path = '')
	{
		return plugin_dir_url(dirname(__DIR__)) . ltrim($path, '/');
	}
}
