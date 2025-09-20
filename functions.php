<?php
/**
 * InfoKiosque functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage InfoKiosque
 * @since InfoKiosque 1.0
 */

require_once __DIR__ . '/vendor/autoload.php';

define('APP_THEME_DIR', __DIR__ . '/');
define('APP_THEME_URL', get_stylesheet_directory_uri());


// Adds theme support for post formats.
if ( ! function_exists( 'infokiosque_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	* @since InfoKiosque 1.0
	 *
	 * @return void
	 */
	function infokiosque_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'infokiosque_post_format_setup' );

// Register options and load additional functionality
add_action('init', 'app_init', 0);

function app_init()
{
	require_once APP_THEME_DIR . 'inc/blocks.php';
	require_once APP_THEME_DIR . 'inc/enqueues.php';
}

// Enqueues style.css on the front.
if ( ! function_exists( 'infokiosque_enqueue_styles' ) ) :
	/**
	 * Enqueues style.css on the front.
	 *
	 * @since InfoKiosque 1.0
	 *
	 * @return void
	 */
	function infokiosque_enqueue_styles() {
		wp_enqueue_style(
			'infokiosque-style',
			get_parent_theme_file_uri( 'style.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
	add_action( 'wp_enqueue_scripts', 'infokiosque_enqueue_styles' );

// Registers custom block styles.
if ( ! function_exists( 'infokiosque_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since InfoKiosque 1.0
	 *
	 * @return void
	 */
	function infokiosque_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'infokiosque' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
	}
endif;
add_action( 'init', 'infokiosque_block_styles' );

// Registers pattern categories.
if ( ! function_exists( 'infokiosque_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since InfoKiosque 1.0
	 *
	 * @return void
	 */
	function infokiosque_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'infokiosque' ),
				'description' => __( 'A collection of full page layouts.', 'infokiosque' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'infokiosque' ),
				'description' => __( 'A collection of post format patterns.', 'infokiosque' ),
			)
		);
	}
endif;
add_action( 'init', 'infokiosque_pattern_categories' );

// Registers block binding sources.
if ( ! function_exists( 'infokiosque_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since InfoKiosque 1.0
	 *
	 * @return void
	 */
	function infokiosque_register_block_bindings() {
		register_block_bindings_source(
			'infokiosque/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'infokiosque' ),
				'get_value_callback' => 'infokiosque_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'infokiosque_register_block_bindings' );

// Registers block binding callback function for the post format name.
if ( ! function_exists( 'infokiosque_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	* @since InfoKiosque 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function infokiosque_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;
