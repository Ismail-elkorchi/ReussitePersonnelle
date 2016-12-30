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
	?>

		<div class="front-left">
			<section id="text-20" class="widget widget_text">
				<div class="widget-wrap">
					<h4 class="widget-title widgettitle">Concevez une vie simple.</h4>
					<div class="textwidget">
						<p>Les secrets du bien-&ecirc;tre et de l&rsquo;apaisement psychologique se pr&eacute;sentent sous la forme de 7 articles gratuits.</p>
						<p>Le but de ces derniers est de vous redonner les r&ecirc;nes de votre vie, vous permettant ainsi de vous affirmer et d&rsquo;enfin profiter pleinement du temps qui vous est imparti.</p>
					</div>
				</div>
			</section>
		</div>

		<div class="front-right">
			<div class="newsletter">

				<h4 class="widget-title">Newsletter gratuite</h4>
				<p class="center">Livr&eacute;e &agrave; votre bo&icirc;te de r&eacute;ception.</p>
				<div id="mc_embed_signup">

					<form action="https://www.reussitepersonnelle.com/sendy/subscribe" method="POST" accept-charset="utf-8">

						<div id="mc_embed_signup_scroll">

							<div class="mc-field-group">
								<label for="name">Pr&eacute;nom</label>
								<input type="text" name="name" id="name" autocomplete="given-name"/>
							</div>

							<div class="mc-field-group">
								<label for="email">Email</label><br/>
								<input type="email" name="email" id="email" autocomplete="email"/>
							</div>
							<div class="clear"></div>
							<input type="hidden" name="list" value="s892DRweYGXUU9hnxLNe1dsQ"/>
							<input type="submit" value="Rejoindre gratuitement" name="submit" id="submit" class="button">
						</div>
					</form>
				</div>
			</div>
		</div>

	<?php
}

genesis();
