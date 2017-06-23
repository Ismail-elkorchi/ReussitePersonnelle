<?php
add_filter('genesis_pre_get_option_site_layout', '__genesis_return_full_width_content');
remove_action('genesis_after_header', 'genesis_do_subnav');

add_action('genesis_before_content','reussitepersonnelle_homepage');
function reussitepersonnelle_homepage() {
	get_template_part('lib/partials/home');
}

remove_action( 'genesis_loop', 'genesis_do_loop' );

/** Unregister the superfish scripts */
add_action( 'wp_enqueue_scripts', 'unregister_scripts' );
function unregister_scripts() {
    wp_deregister_script( 'superfish' );
    wp_deregister_script( 'superfish-args' );
    //wp_deregister_script( 'jquery' );
    wp_deregister_script( 'comment-reply' );
}

genesis();
