<?php
/*
Template Name: Page de vente
*/

// Ajouter une classe body personnalisée  à la l'entete "head"
add_filter( 'body_class', 'add_body_class' );
function add_body_class( $classes ) {
   $classes[] = 'page-de-vente';
   return $classes;
}

//* Unregister primary/secondary navigation menus
remove_theme_support( 'genesis-menus' );


//* Remove the header right widget area
unregister_sidebar( 'header-right' );

// Remove header, navigation, breadcrumbs, footer widgets, footer
//remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
//remove_action( 'genesis_header', 'genesis_do_header' );
//remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );
//remove_action( 'genesis_after_header', 'genesis_do_nav' );
//remove_action( 'genesis_after_header', 'genesis_do_subnav' );
//remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs');
//remove_action( 'genesis_before_footer', 'genesis_footer_widget_areas' );
//remove_action('genesis_before_footer', 'include_footer_widgets');
//remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
//remove_action( 'genesis_footer', 'genesis_do_footer' );
//remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );
//remove_action('wp_footer', 'include_scripts');

//* Add Extra Code to Primary Menu
function reussitepersonnelle_commander() {
	?>
	<li class="genesis-nav-menu menu-item commander"><a onclick="javascript:__gaTracker('send', 'event', 'top-nav', 'commander');" href="https://www.reussitepersonnelle.com/commande/?add-to-cart=7782">Commander Maintenant</a></li>';
	<?php
}
add_action('genesis_header_right','reussitepersonnelle_commander', 9, 2);

/** Unregister the superfish scripts */
add_action( 'wp_enqueue_scripts', 'unregister_scripts' );
function unregister_scripts() {
    wp_deregister_script( 'superfish' );
    wp_deregister_script( 'superfish-args' );
  //  wp_deregister_script( 'jquery' );
    wp_deregister_script( 'comment-reply' );
}
genesis();
