<?php
/**
 * Plugin Name:       AI Pilot Blocks
 * Plugin URI:        https://github.com/EVGexpert/ai-pilot-wp-plugin
 * Description:       Расширяемая библиотека Gutenberg/FSE-блоков, подготовленная для управления через AI Pilot и MCP Abilities.
 * Version:           1.1.0
 * Requires at least: 6.9
 * Tested up to:      7.0
 * Requires PHP:      8.0
 * License:           GPL-2.0-or-later
 * Text Domain:       ai-pilot-blocks
 *
 * @package AIPilotBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AIPILOT_BLOCKS_VERSION', '1.1.0' );
define( 'AIPILOT_BLOCKS_PATH', plugin_dir_path( __FILE__ ) );
define( 'AIPILOT_BLOCKS_URL', plugin_dir_url( __FILE__ ) );

require_once AIPILOT_BLOCKS_PATH . 'includes/rules.php';
require_once AIPILOT_BLOCKS_PATH . 'includes/manifest.php';

function aipilot_blocks_categories( array $categories ): array {
	array_unshift(
		$categories,
		array(
			'slug'  => 'aipilot',
			'title' => __( 'AI Pilot', 'ai-pilot-blocks' ),
			'icon'  => 'superhero',
		)
	);
	return $categories;
}
add_filter( 'block_categories_all', 'aipilot_blocks_categories' );

function aipilot_blocks_register(): void {
	wp_register_style(
		'aipilot-blocks-style',
		AIPILOT_BLOCKS_URL . 'assets/blocks.css',
		array(),
		(string) filemtime( AIPILOT_BLOCKS_PATH . 'assets/blocks.css' )
	);
	wp_register_style(
		'aipilot-blocks-editor-style',
		AIPILOT_BLOCKS_URL . 'assets/editor.css',
		array( 'wp-edit-blocks' ),
		(string) filemtime( AIPILOT_BLOCKS_PATH . 'assets/editor.css' )
	);
	wp_register_script(
		'aipilot-blocks-editor-utils',
		AIPILOT_BLOCKS_URL . 'assets/editor-utils.js',
		array( 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n', 'wp-server-side-render' ),
		(string) filemtime( AIPILOT_BLOCKS_PATH . 'assets/editor-utils.js' ),
		true
	);

	$directories = glob( AIPILOT_BLOCKS_PATH . 'blocks/*', GLOB_ONLYDIR );
	if ( ! is_array( $directories ) ) {
		return;
	}

	foreach ( $directories as $directory ) {
		if ( file_exists( $directory . '/block.json' ) ) {
			register_block_type( $directory );
		}
	}
}
add_action( 'init', 'aipilot_blocks_register' );

function aipilot_blocks_register_pattern_category(): void {
	register_block_pattern_category(
		'aipilot-blocks',
		array( 'label' => __( 'AI Pilot Blocks', 'ai-pilot-blocks' ) )
	);
}
add_action( 'init', 'aipilot_blocks_register_pattern_category' );

/**
 * Public PHP integration point for AI Pilot Remote Site API and other connectors.
 */
function aipilot_blocks_manifest(): array {
	return AIPilot_Blocks_Manifest::get();
}
