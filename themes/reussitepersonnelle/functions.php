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
		add_theme_support( 'html5', array( 'search-form' ) );
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
	'init',
	static function (): void {
		register_block_pattern(
			'reussitepersonnelle/navigation-overlay-editorial',
			array(
				'title'      => __( 'Editorial navigation overlay', 'reussitepersonnelle' ),
				'categories' => array( 'navigation' ),
				'blockTypes' => array( 'core/template-part/navigation-overlay' ),
				'content'    => '<!-- wp:group {"className":"rp-navigation-overlay","layout":{"type":"constrained"}} -->
<div class="wp-block-group rp-navigation-overlay"><!-- wp:group {"className":"rp-navigation-overlay-bar","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"nowrap"}} -->
<div class="wp-block-group rp-navigation-overlay-bar"><!-- wp:paragraph {"className":"rp-section-label"} -->
<p class="rp-section-label">' . esc_html__( 'Menu', 'reussitepersonnelle' ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:navigation-overlay-close {"className":"rp-navigation-overlay-close"} /--></div>
<!-- /wp:group -->

<!-- wp:site-title {"level":0,"className":"rp-navigation-overlay-title"} /-->

<!-- wp:paragraph {"className":"rp-navigation-overlay-intro"} -->
<p class="rp-navigation-overlay-intro">' . esc_html__( 'Choisissez un point d’entrée, cherchez un sujet précis, puis revenez à une lecture calme.', 'reussitepersonnelle' ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:navigation {"className":"rp-overlay-nav","overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"}} /-->

<!-- wp:search {"label":"' . esc_attr__( 'Rechercher sur Réussite Personnelle', 'reussitepersonnelle' ) . '","showLabel":false,"placeholder":"' . esc_attr__( 'Rechercher un sujet', 'reussitepersonnelle' ) . '","buttonText":"' . esc_attr__( 'Rechercher', 'reussitepersonnelle' ) . '","className":"rp-search-form rp-overlay-search"} /-->

<!-- wp:reussitepersonnelle/topic-links /--></div>
<!-- /wp:group -->',
			)
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
