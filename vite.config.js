import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin'
import { wordpressPlugin, wordpressThemeJson } from '@roots/vite-plugin';

// Set APP_URL if it doesn't exist for Laravel Vite plugin
if (!process.env.APP_URL) {
	process.env.APP_URL = 'http://localhost:8000';
}

export default defineConfig({
	base: '/wp-content/plugins/acorn-plugin/public/build/',
	plugins: [
		tailwindcss(),
		laravel({
			input: [
				'resources/css/app.css',
				'resources/js/app.js',
			],
			refresh: true,
		}),

		wordpressPlugin(),

		// Generate the theme.json file in the public/build/assets directory
		// based on the Tailwind config and the theme.json file from base theme folder
		wordpressThemeJson({
			disableTailwindColors: false,
			disableTailwindFonts: false,
			disableTailwindFontSizes: false,
		}),
	],
	resolve: {
		alias: {
			'@scripts': '/resources/js',
			'@styles': '/resources/css',
		},
	},
})
