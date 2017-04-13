<?php
/*
Template Name: Page newsletter
*/

remove_action( 'wp_enqueue_scripts', 'edd_load_scripts' );
remove_action( 'wp_enqueue_scripts', 'edd_register_styles' );

// Ajouter une classe body personnalisée  à la l'entete "head"
add_filter( 'body_class', 'add_body_class' );
function add_body_class( $classes ) {
   $classes[] = 'page-newsletter full-width';
   return $classes;
}

//* Unregister primary/secondary navigation menus
remove_theme_support( 'genesis-menus' );


//* Remove the header right widget area
unregister_sidebar( 'header-right' );


//* Remove the entry title (requires HTML5 theme support)
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );



/** Unregister the superfish scripts */
add_action( 'wp_enqueue_scripts', 'unregister_scripts' );
function unregister_scripts() {
    wp_deregister_script( 'superfish' );
    wp_deregister_script( 'superfish-args' );
  //  wp_deregister_script( 'jquery' );
    wp_deregister_script( 'comment-reply' );
}

add_action('genesis_before_loop', 'reussitepersonnelle_newsletter');

function reussitepersonnelle_newsletter(){
get_template_part('lib/partials/newsletter-subscription');
}

genesis();
