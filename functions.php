<?php

define('GENESIS_LANGUAGES_DIR', STYLESHEETPATH.'/languages');
define('GENESIS_LANGUAGES_URL', STYLESHEETPATH.'/languages');

// Start the engine
require_once(TEMPLATEPATH.'/lib/init.php');

// Woocommerce Customisition
require_once( CHILD_DIR . '/lib/woocommerce/woocommerce.php' );

remove_action( 'wp_head', 'genesis_load_favicon' );
add_action('wp_head', 'reussitepersonnelle_favicon_link');
function reussitepersonnelle_favicon_link() {
	echo '<link rel="shortcut icon" href="data:image/x-icon;base64,iVBORw0KGgo=" />';
}

//Desactivate Wordpress XML RPC
add_filter('xmlrpc_enabled', '__return_false');

//* Child theme
define( 'CHILD_THEME_NAME', __( 'Reussite Personnelle 2015', 'reussitepersonnelle' ) );
define( 'CHILD_THEME_URL', 'https://www.reussitepersonnelle.com' );
define( 'CHILD_THEME_VERSION', '1.0' );

//* Add HTML5 markup structure
add_theme_support( 'html5' );

//* Add Viewport meta tag for mobile browsers (requires HTML5 theme support)
add_theme_support( 'genesis-responsive-viewport' );

// Remove the date and time on comments in Genesis child themes
add_filter( 'genesis_show_comment_date', '__return_false' );

// Replace gravatar with the first letter of the commentator's name
add_filter('get_avatar', 'reussitepersonnelle_gravatar_filter', 10, 5);
function reussitepersonnelle_gravatar_filter($avatar) {
	if( have_comments() ){
		$author = get_comment_author();
		return '<span class="avatar">' . mb_substr( $author, 0, 1 ) . '</span>';
	} else {
		return $avatar;
	}
}

//* Return articles only when search
add_filter('pre_get_posts','SearchFilter');
function SearchFilter($query) {
	if ($query->is_search) {
		$query->set('post_type', 'post');
	}
	return $query;
}

/** Move Breadcrumbs Below Main Nav **/
remove_action('genesis_before_loop', 'genesis_do_breadcrumbs');
add_action('genesis_before_content', 'genesis_do_breadcrumbs');

//* Customize the entry meta in the entry header
add_filter( 'genesis_post_info', 'reussitepersonnelle_post_info_filter' );
function reussitepersonnelle_post_info_filter($post_info) {
	$post_info = 'Par [post_author] [post_comments] [post_edit]';
	return $post_info;
}

//* Remove the entry meta in the entry footer (requires HTML5 theme support)
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

//* Reposition the primary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav' );

//* Unregister secondary navigation menu
add_theme_support( 'genesis-menus', array( 'primary' => __( 'Primary Navigation Menu', 'genesis' ) ) );

//* Force full-width-content layout setting
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

//* Unregister layout settings
genesis_unregister_layout( 'content-sidebar' );
genesis_unregister_layout( 'sidebar-content' );
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

//* Unregister sidebars
unregister_sidebar( 'header-right' );
unregister_sidebar( 'sidebar' );
unregister_sidebar( 'sidebar-alt' );

//* Remove the site description
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

//* Remove comment form allowed tags
add_filter( 'comment_form_defaults', 'sp_remove_comment_form_allowed_tags' );
function sp_remove_comment_form_allowed_tags( $defaults ) {

	$defaults['comment_notes_after'] = '';
	return $defaults;

}

/**
 * Disable the emoji's
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @param    array  $plugins
 * @return   array             Difference betwen the two arrays
 */
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

//* Add Extra Code to Primary Menu
add_filter('wp_nav_menu_items','reussitepersonnelle_menu_extras', 10, 2);
function reussitepersonnelle_menu_extras($menu, $args) {
	ob_start();
	get_template_part('lib/partials/menu-extra');
	return $menu . ob_get_clean();
}

//* Remove Jquery migrate
add_filter( 'wp_default_scripts', 'reussitepersonnelle_remove_jquery_migrate' );
function reussitepersonnelle_remove_jquery_migrate( &$scripts) {
  if(!is_admin()) {
    $scripts->remove( 'jquery');
    $scripts->add( 'jquery', false, array( 'jquery-core' ), '1.12.4' );
  }
}

// Enqueue global js script
add_action( 'wp_enqueue_scripts', 'reussitepersonnelle_enqueue_global_script' );
function reussitepersonnelle_enqueue_global_script() {
	wp_enqueue_script( 'reussitepersonnelle-global-script', get_stylesheet_directory_uri() . '/lib/js/global.js', array( 'jquery' ), '1.0.0', true );
}


//* Customize the site footer
remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
remove_action( 'genesis_footer', 'genesis_do_footer' );
remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );
add_action( 'genesis_footer', 'reussitepersonnelle_custom_footer' );
function reussitepersonnelle_custom_footer() {
	get_template_part('lib/partials/footer');
}




// Google Analytics custom dimenstions
/*
function GA_custom_author($gaq_push){
	$author_id = $this_post->post_author;
	$name = get_the_author_meta('display_name', $author_id);

	$gaq_push = "'set', 'dimension1', $name";
	return $gaq_push;
}
add_filter('yst_ga_filter_push_vars', 'GA_custom_author');

*/
