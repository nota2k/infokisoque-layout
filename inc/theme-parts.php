<?php
/*
 * Helper functions that resemble `get_template_part()`; there are 2 notable differences:
 *
 *  1. Concise interface
 *     Instead of this:
 *       ```
 *       get_template_part('content', 'featured', [ 'title' => get_field('featured_title') ])
 *       ```
 *     you do this:
 *       ```
 *       app_render_template_part('content/featured', [ 'title' => get_field('featured_title') ])
 *       ```
 *
 *  2. The functions in this helper file will extract variables; with get_template_part(), the parts
 *     need to read the passed data through the `$args` variable that's injected from the core wp functions
 */

if ( ! function_exists( 'app_render_part' ) ) {
	/**
	 * Render a file from theme's "fragments" directory.
	 *
	 * @param string $fragment_path path to the file, might include "fragments" prefix or not
	 * @param array $context variables to be exposed in the fragment
	 * @return void
	 */
	function app_render_part( string $fragment_path, ?array $context = [] ) {
		// Add "fragments/" prefix to the path, if it's not there already
		$part_path = preg_replace(
			'/^\/?(parts\/)?(.*?)(\.php)?$/i',
			'parts/$2.php',
			$part_path
		);

		app_render_template_part( $part_path, $context );
	}
}

if ( ! function_exists( 'app_render_template_part' ) ) {
	/**
	 * Render a file from arbitrary place in the theme directory
	 *
	 * @param string $fragment_path path to the file, might include "fragments" prefix or not
	 * @param array $context variables to be exposed in the fragment
	 * @return void
	 */
	function app_render_template_part( $path, ?array $context = [] ) {
		// the variable prefix aims to avoid collisions while extract()-ing the $context
		$__template_file = locate_template( $path, false, false );

		if (!$__template_file) {
			throw new \RuntimeException( 'Could not locate template for: ' . $path );
		}

		extract( $context, EXTR_SKIP );

		ob_start();

		// Intentionally don't use require_once() -- a fragment might be used multiple times
		// in the same template
		require( $__template_file );

		echo ob_get_clean();
	}
}
