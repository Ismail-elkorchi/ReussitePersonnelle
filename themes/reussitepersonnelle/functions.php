<?php
/**
 * Theme bootstrap.
 *
 * @package ReussitePersonnelle
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	static function (): void {
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'html5', array( 'search-form', 'style', 'script' ) );
		add_editor_style( 'style.css' );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		wp_enqueue_style(
			'reussitepersonnelle-style',
			get_stylesheet_uri(),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
);

add_action(
	'wp_head',
	static function (): void {
		if ( ! is_front_page() ) {
			return;
		}

		printf(
			'<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n",
			esc_url( get_theme_file_uri( 'assets/images/homepage-reflection.png' ) )
		);
	},
	1
);

add_action(
	'admin_notices',
	static function (): void {
		if ( defined( 'REUSSITEPERSONNELLE_CORE_VERSION' ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		printf(
			'<div class="notice notice-error"><p>%s</p></div>',
			esc_html__( 'The Reussite Personnelle theme requires the Reussite Personnelle Core plugin to render site-specific blocks and navigation.', 'reussitepersonnelle' )
		);
	}
);
