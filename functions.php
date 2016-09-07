<?php

define('GENESIS_LANGUAGES_DIR', STYLESHEETPATH.'/languages');
define('GENESIS_LANGUAGES_URL', STYLESHEETPATH.'/languages');

// Start the engine
require_once(TEMPLATEPATH.'/lib/init.php');

remove_action( 'wp_head', 'genesis_load_favicon' );

//Desactivate Wordpress XML RPC
add_filter('xmlrpc_enabled', '__return_false');


//* Edd Customisition
require(CHILD_DIR.'/lib/edd_templates/functions.php');
//remove_action( 'wp_enqueue_scripts', 'edd_register_styles' );
//	remove_action( 'wp_enqueue_scripts', 'edd_load_scripts' );

//* Child theme
define( 'CHILD_THEME_NAME', __( 'Reussite Personnelle 2015', 'reussitepersonnelle' ) );
define( 'CHILD_THEME_URL', 'https://www.reussitepersonnelle.com' );
define( 'CHILD_THEME_VERSION', '1.0' );

//* Add HTML5 markup structure
add_theme_support( 'html5' );

//* Add Viewport meta tag for mobile browsers (requires HTML5 theme support)
add_theme_support( 'genesis-responsive-viewport' );

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'reussitepersonnelle_enqueue_scripts_styles' );
function reussitepersonnelle_enqueue_scripts_styles() {
	// Enqueue dashicons style
	wp_enqueue_style( 'dashicons' );
}

// Remove the date and time on comments in Genesis child themes
add_filter( 'genesis_show_comment_date', '__return_false' );



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
add_action('genesis_before_content', 'focus_do_breadcrumbs');

function focus_do_breadcrumbs() {


	if (
		( ( is_front_page() || is_home() ) && ! genesis_get_option( 'breadcrumb_home' ) ) ||
		( is_single() && ! genesis_get_option( 'breadcrumb_single' ) ) ||
		( is_page() && ! is_page('950') ) ||
		( ( is_archive() || is_search() ) && ! genesis_get_option( 'breadcrumb_archive' ) ) ||
		( is_404() && ! genesis_get_option( 'breadcrumb_404' ) ) ||
		( is_attachment() && ! genesis_get_option( 'breadcrumb_attachment' ) )
	)
		return;

	if ( function_exists( 'bcn_display' ) ) {
		echo '<div class="breadcrumb">';
		bcn_display();
		echo '</div>';
	}
	elseif ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<div class="breadcrumb">', '</div>' );
	}
	elseif ( function_exists( 'breadcrumbs' ) ) {
		breadcrumbs();
	}
	elseif ( function_exists( 'crumbs' ) ) {
		crumbs();
	}
	else {
		genesis_breadcrumb();
	}

}


//* Add Blog Link to the BreadCrumb
function add_blog_link_to_breadcrumb($links){

	if(!is_page()){

		$output = array_slice($links, 0, 1);
		$output[1] = array('url' => 'https://www.reussitepersonnelle.com/blog/', 'text' => 'Blog');
		$output[2] = $links[1];
		if (!is_category() && !is_archive() && !is_search()){
			$output[3] = $links[2];
		}
		return $output;
	}else{
		return 	$links;
	}
}
add_filter( 'wpseo_breadcrumb_links', 'add_blog_link_to_breadcrumb' );



//* Customize the post info function
add_filter( 'genesis_post_info', 'post_info_filter' );
function post_info_filter($post_info) {
if (!is_page()) {
    $post_info = 'Par [post_author] [post_comments] [post_edit]';
    return $post_info;
}}


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
function reussitepersonnelle_menu_extras($menu, $args) {
	
	$extras = '<li class="menu-item changez-votre-vie"><a onclick="javascript:__gaTracker(\'send\', \'event\', \'top-nav\', \'changer-de-vie\');" href="https://www.reussitepersonnelle.com/changer-de-vie/">Changez votre vie</a></li>';
	$extras .= '<li class="search"><a id="main-nav-search-link" class="icon-search"></a><div class="search-div">' . get_search_form( false ) . '</div></li>';

	return $menu . $extras;
}
add_filter('wp_nav_menu_items','reussitepersonnelle_menu_extras', 9, 2);



//* Enqueue Google fonts
add_action( 'wp_enqueue_scripts', 'reussitepersonnelle_google_fonts' );
function reussitepersonnelle_google_fonts() {
	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Montserrat:300,400,700|Neuton:700,300,400', array(), CHILD_THEME_VERSION );
}

// Enqueue global js script
add_action( 'wp_enqueue_scripts', 'reussitepersonnelle_enqueue_global_script' );
function reussitepersonnelle_enqueue_global_script() {
	wp_enqueue_script( 'reussitepersonnelle-global-script', get_stylesheet_directory_uri() . '/lib/js/global.js', array( 'jquery' ), '1.0.0', true );
}



//* Hook sticky message before site header
add_action( 'genesis_after', 'mobile_first_sticky_message' );
function mobile_first_sticky_message() {
	if(is_single()){


		?>
	        <div class="sticky-message">
	            <p>Attirez la prosp&eacute;rit&eacute; et le succ&egrave;s dans votre vie.</p>
	            <a onclick="javascript:__gaTracker('send', 'event', 'sticky-button', 'changer-de-vie');" href="https://www.reussitepersonnelle.com/changer-de-vie/">Changez votre vie</a>
	        </div>
	    <?php
	}
}

//Add Footer Newsletter Call to action
add_action('genesis_before_footer', 'reussitepersonnelle_newsletter_footer');
function reussitepersonnelle_newsletter_footer(){
	if(is_single() && get_comments_number() > 1){
		?>
		<div class="newsletter-footer">
			<div class="wrap">
				<h4 class="widget-title widgettitle">&Ecirc;tes-vous pr&ecirc;t &agrave; faire passer votre vie au niveau sup&eacute;rieur ?</h4>
				<p>Rejoignez la communaut&eacute; R&eacute;ussite Personnelle. Ce sera plus amusant que vous pensez!</p>
				<p><a class="button" href="https://www.reussitepersonnelle.com/newsletter/">s&#39;inscrire</a></p>
			</div>
		</div>
	<?php
	}
}

//* Customize the site footer
remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
remove_action( 'genesis_footer', 'genesis_do_footer' );
remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );
add_action( 'genesis_footer', 'reussitepersonnelle_custom_footer' );
function reussitepersonnelle_custom_footer() { ?>

	<div class="site-footer">
		<div class="wrap">
			<div class="left">
				<p>
					R&eacute;ussite Personnelle &copy; 2012-<?php echo date('Y'); ?>  &middot; <a href="https://www.reussitepersonnelle.com/politique-confidentialite/">Politique de confidentialit&eacute;</a> | <a href="https://www.reussitepersonnelle.com/politique-de-remboursement/">Politique de remboursement</a>
				</p>
			</div>
			<div class="right">
				<p>Langues &middot; <a href="https://www.reussitepersonnelle.com/en/">Anglais</a> | <a href="https://www.reussitepersonnelle.com/ar/">Arabe</a> | Fran&ccedil;ais</p>
			</div>
		</div>
	</div>

<?php
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
