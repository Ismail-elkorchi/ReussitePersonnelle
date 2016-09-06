<?php
	
remove_action( 'wp_enqueue_scripts', 'edd_load_scripts' );
remove_action( 'wp_enqueue_scripts', 'edd_register_styles' );

add_action('genesis_entry_footer', 'reussitepersonnelle_ad');

function reussitepersonnelle_ad(){
	?>
		<div class="single-calltoaction">
			<h3>Attirez la prosp&eacute;rit&eacute; et le succ&egrave;s dans votre vie</h3>
			<p>&laquo; Changer de vie : le guide d&rsquo;un pas vers le bonheur &raquo;, est une m&eacute;thode originale qui va r&eacute;volutionner votre quotidien et vous permettre de devenir la personne que vous r&ecirc;vez secr&egrave;tement d&rsquo;&ecirc;tre !</p>
			<a class="button" onclick="javascript:__gaTracker('send', 'event', 'outbound-single-btn', 'changer-de-vie');" target="_blank" href="https://www.reussitepersonnelle.com/changer-de-vie/">Changez votre vie!</a>
		</div>
	<?php
}


function reussitepersonnelle_social_sharing_buttons() {
	
		// Get current page URL
		$shortURL = get_permalink();
		
		// Get current page title
		$shortTitle = get_the_title();
		
		// Construct sharing URL without using any script
		$twitterURL = 'https://twitter.com/intent/tweet?text='.$shortTitle.'&amp;url='.$shortURL.'&amp;via=ReussitePerso';
		$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u='.$shortURL;
		$googleURL = 'https://plus.google.com/share?url='.$shortURL;
		
		//Get social shares count

		$facebookCountUrl = 'https://graph.facebook.com/?id=' . $shortURL;
		$responsefacebook = wp_remote_post( $facebookCountUrl, array(
			'method'		=> 'GET',
			'timeout' => 120,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers'		=> array('Content-Type' => 'Content-type: application/json'),
		    )
		);
		$facebookJson = json_decode($responsefacebook['body'], true);
		$facebookCountNumber = isset($facebookJson['share'])?intval($facebookJson['share']['share_count']):0;
		
		$googleCountUrl = 'https://clients6.google.com/rpc';
		$responseGoogle = wp_remote_post( $googleCountUrl, array(
			'method'		=> 'POST',
			'timeout' => 120,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers'		=> array('Content-Type' => 'Content-type: application/json'),
			'body' => '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.$shortURL.'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"AIzaSyBKkTJzo2D6mxH-etRQU_HLh_azAqBbh7Y","apiVersion":"v1"}]'
				)
		    );
		
		$googleJson = json_decode($responseGoogle['body'], true);
		$googleCountNumber = isset($googleJson[0]['result']['metadata']['globalCounts']['count'])?intval($googleJson[0]['result']['metadata']['globalCounts']['count']):0;
	
		// Add sharing button at the end of page/page content
		?>
		<div class="share-before share-outlined share-small">
			<div class="twitter sharrre">
				<div class="box no-count">
						<a class="share" onclick="window.open( '<?php echo $twitterURL ?>', 'newwindow', 'width=520, height=250'); return false;" href="<?php echo $twitterURL ?>">Tweeter</a>
				</div>
			</div>
			<div class="facebook sharrre">
				<div class="box">
					<a class="count" onclick="window.open('<?php echo $facebookURL ?>', 'newwindow', 'width=520, height=250'); return false;" href="<?php echo $facebookURL ?>"><?php echo $facebookCountNumber ?></a>
					<a class="share" onclick="window.open('<?php echo $facebookURL ?>', 'newwindow', 'width=520, height=250'); return false;" href="<?php echo $facebookURL ?>">Partager</a>
				</div>
			</div>
			<div class="googlePlus sharrre">
				<div class="box">
					<a class="count" onclick="window.open('<?php echo $googleURL ?>', 'newwindow', 'width=520, height=250'); return false;" href="<?php echo $googleURL ?>"><?php echo $googleCountNumber ?></a>
					<a class="share" onclick="window.open('<?php echo $googleURL ?>', 'newwindow', 'width=520, height=250'); return false;" href="<?php echo $googleURL ?>">Partager</a>
				</div>
			</div>
		</div>
		<?php
};
add_action( 'genesis_entry_header', 'reussitepersonnelle_social_sharing_buttons', 14);

genesis();