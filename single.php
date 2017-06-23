<?php

add_action('genesis_entry_footer', 'reussitepersonnelle_ad');
function reussitepersonnelle_ad(){
	get_template_part('lib/partials/single-calltoaction');
}

//Add Footer Newsletter Call to action
add_action('genesis_before_footer', 'reussitepersonnelle_newsletter_footer');
function reussitepersonnelle_newsletter_footer(){
	if( get_comments_number() > 1 ){
		get_template_part('lib/partials/newsletter-banner');
	}
}

add_action( 'genesis_entry_header', 'reussitepersonnelle_social_sharing_buttons', 14);
function reussitepersonnelle_social_sharing_buttons() {
		get_template_part('lib/partials/social-share');
}

genesis();
