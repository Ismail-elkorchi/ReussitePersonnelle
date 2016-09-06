<?php
add_filter('genesis_pre_get_option_site_layout', '__genesis_return_full_width_content');
remove_action('genesis_after_header', 'genesis_do_subnav');

remove_action( 'wp_enqueue_scripts', 'edd_load_scripts' );
remove_action( 'wp_enqueue_scripts', 'edd_register_styles' );

add_action('genesis_before_content','reussitepersonnelle_homepage');
function reussitepersonnelle_homepage()
{
	?>
		<div class="home-intro">
			<div class="wrap">
				<h2>Le guide qui changera votre vie</h2>
				<div class="calltoaction">
					<p>Le bonheur est &agrave; la port&eacute;e de tous... <br />&agrave; condition d'accepter l'&eacute;volution personnelle et ses implications</p>
					<a onclick="javascript:__gaTracker('send', 'event', 'home-button', 'changer-de-vie');" class="button" href="https://www.reussitepersonnelle.com/changer-de-vie/">D&eacute;couvrez notre guide</a>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<div class="home-features">
			<div class="wrap">
					<div class="one-third first">
						<div class="dashicons dashicons-welcome-write-blog"></div>
						<h4>Blog</h4>
						<p class="margin-bottom">Nos articles gratuits, accessible depuis le blog, vous aidons &agrave; assurer votre &eacute;panouissement personnel au quotidien.</p>
						<p><a class="button" href="https://www.reussitepersonnelle.com/blog/">Lire le blog</a></p>
					</div>
					<div class="one-third">
						<div class="dashicons dashicons-rss"></div>
						<h4>Newsletter</h4>
						<p class="margin-bottom">Recevez nos conseils exclusifs dir&eacute;ctement sur votre boite email. Votre vie ne sera plus jamais la m&ecirc;me.</p>
						<p><a class="button" href="https://www.reussitepersonnelle.com/newsletter/">Inscrivez-vous</a></p>
					</div>
					<div class="one-third last">
						<div class="dashicons dashicons-desktop"></div>
						<h4>&Agrave; propos</h4>
						<p class="margin-bottom">Notre site vous donne les outils pour vous aider &agrave; d&eacute;velopper une existence sereine et heureuse.</p>
						<p><a class="button" href="https://www.reussitepersonnelle.com/a-propos/">En savoir plus</a></p>
					</div>
			</div>
		</div>
	<?php	
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