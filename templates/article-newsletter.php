<?php
/*
Template Name: Article newsletter
*/

remove_action( 'wp_enqueue_scripts', 'edd_load_scripts' );
remove_action( 'wp_enqueue_scripts', 'edd_register_styles' );

// Ajouter une classe body personnalisée  à la l'entete "head"
add_filter( 'body_class', 'add_body_class' );
function add_body_class( $classes ) {
   $classes[] = 'article-newsletter full-width';
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

/** Unregister the superfish scripts */
add_action( 'wp_enqueue_scripts', 'unregister_scripts' );
function unregister_scripts() {
    wp_deregister_script( 'superfish' );
    wp_deregister_script( 'superfish-args' );
  //  wp_deregister_script( 'jquery' );
    wp_deregister_script( 'comment-reply' );
}

add_action('genesis_entry_footer', 'reussitepersonnelle_ad');

function reussitepersonnelle_ad(){
	get_template_part('lib/partials/single-calltoaction');
}

genesis();
