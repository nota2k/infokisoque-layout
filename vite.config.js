import uupVite from 'vite-plugin-uup';
import autoprefixer from 'autoprefixer';
import liveReload from 'vite-plugin-live-reload';
import { checker } from 'vite-plugin-checker';
import externalGlobals from 'rollup-plugin-external-globals';
import { defineConfig } from 'vite';

const port = process.env.VITE_PORT || 1559;

export default defineConfig({
	input: [
		"src/js/main.js",
		"src/js/editor.js",
		"src/scss/theme.scss",
		"src/scss/block-editor.scss",
	],
	port,

	plugins: [
		uupVite(),

		liveReload(__dirname + "/**/**.php"),

		checker({
			stylelint: {
				lintCommand: "stylelint 'src/scss/**/*.scss' --fix",
			},
		}),

		externalGlobals({
			jquery: "jQuery",
		}),
	],
	css: {
		postcss: {
			plugins: [autoprefixer()],
		},
		devSourcemap: true,
	},
	resolve: {
		alias: {
			"@mixins": "/src/scss/generic/mixins",
			"@functions": "/src/scss/generic/functions",
			"@variables": "/src/scss/generic/variables",
			"@components": "/src/scss/components",
			"@generic": "/src/scss/generic",
		},
	},
});
