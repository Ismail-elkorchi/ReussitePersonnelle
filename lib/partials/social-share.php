<?php
// Get current page URL
$pageURL = get_permalink();

// Get current page title
$pageTitle = get_the_title();

// Construct sharing URLs
$twitterUser = is_rtl() ? 'anajahadati' : 'ReussitePerso';
$twitterURL = 'https://twitter.com/intent/tweet?text=' . $pageTitle . '&amp;url=' . $pageURL . '&amp;via=' . $twitterUser;
$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u=' . $pageURL;
$googleURL = 'https://plus.google.com/share?url=' . $pageURL;

?>
<div class="social-sharing">
	<div class="twitter share-button">
		<svg id="twitter-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="#09b0ed"><path d="M18.94 4.46c-.49.73-1.11 1.38-1.83 1.9.01.15.01.31.01.47 0 4.85-3.69 10.44-10.43 10.44-2.07 0-4-.61-5.63-1.65.29.03.58.05.88.05 1.72 0 3.3-.59 4.55-1.57-1.6-.03-2.95-1.09-3.42-2.55.22.04.45.07.69.07.33 0 .66-.05.96-.13-1.67-.34-2.94-1.82-2.94-3.6v-.04c.5.27 1.06.44 1.66.46-.98-.66-1.63-1.78-1.63-3.06 0-.67.18-1.3.5-1.84 1.81 2.22 4.51 3.68 7.56 3.83-.06-.27-.1-.55-.1-.84 0-2.02 1.65-3.66 3.67-3.66 1.06 0 2.01.44 2.68 1.16.83-.17 1.62-.47 2.33-.89-.28.85-.86 1.57-1.62 2.02.75-.08 1.45-.28 2.11-.57z"/></svg>
		<a rel="nofollow" target="_blank" data-name="twitter" class="share" href="<?php echo $twitterURL; ?>"><?php esc_html_e('Tweeter', 'reussitepersonnelle'); ?></a>
	</div>
	<div class="facebook share-button">
		<svg id="facebook-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="#3d5a98"><path d="M8.46 18h2.93v-7.3h2.45l.37-2.84h-2.82V6.04c0-.82.23-1.38 1.41-1.38h1.51V2.11c-.26-.03-1.15-.11-2.19-.11-2.18 0-3.66 1.33-3.66 3.76v2.1H6v2.84h2.46V18z"/></svg>
		<a rel="nofollow" target="_blank" data-name="facebook" class="share" href="<?php echo $facebookURL; ?>"><?php esc_html_e('Partager', 'reussitepersonnelle'); ?></a>
	</div>
	<div class="googleplus share-button">
		<svg id="googleplus-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 60 60" fill="#dd4c39"><path d="M52.218 25.852h-7.512v-7.51c0-.573-.465-1.04-1.037-1.04h-2.14c-.576 0-1.04.467-1.04 1.04v7.51h-7.513c-.572 0-1.04.467-1.04 1.04v2.14c0 .574.468 1.04 1.04 1.04h7.512v7.513c0 .574.464 1.04 1.04 1.04h2.14c.57 0 1.036-.466 1.036-1.04V30.07h7.512c.572 0 1.04-.465 1.04-1.04v-2.138c0-.574-.468-1.04-1.04-1.04z"/><path d="M26.974 32.438c-1.58-1.12-3.016-2.76-3.04-3.264 0-.918.08-1.357 2.14-2.96 2.662-2.085 4.128-4.825 4.128-7.72 0-2.625-.802-4.957-2.167-6.595h1.06c.218 0 .433-.07.608-.197l2.955-2.14c.367-.264.52-.733.38-1.162-.14-.427-.536-.72-.987-.72H18.836c-1.446 0-2.915.256-4.357.752-4.816 1.66-8.184 5.765-8.184 9.978 0 5.97 4.624 10.493 10.805 10.635-.122.473-.183.94-.183 1.396 0 .92.233 1.792.713 2.634h-.17c-5.892 0-11.21 2.89-13.23 7.193-.525 1.12-.793 2.25-.793 3.367 0 1.086.28 2.13.826 3.113 1.27 2.27 3.994 4.03 7.677 4.96 1.9.48 3.944.726 6.065.726 1.906 0 3.723-.246 5.403-.732 5.238-1.52 8.625-5.376 8.625-9.827 0-4.27-1.374-6.828-5.06-9.435zm-16.69 9.777c0-3.107 3.946-5.832 8.445-5.832h.12c.98.012 1.934.156 2.834.432.31.213.607.416.893.61 2.084 1.42 3.46 2.358 3.844 3.862.09.38.136.758.136 1.125 0 3.87-2.885 5.83-8.578 5.83-4.315 0-7.695-2.646-7.695-6.027zm4.093-29.357c.703-.803 1.624-1.227 2.658-1.227l.117.003c2.92.086 5.716 3.34 6.23 7.256.29 2.19-.2 4.252-1.3 5.508-.706.805-1.614 1.23-2.69 1.23h-.049c-2.86-.09-5.716-3.468-6.227-7.378-.287-2.186.173-4.15 1.26-5.392z"/></svg>
		<a rel="nofollow" target="_blank" data-name="googleplus" class="share" href="<?php echo $googleURL; ?>"><?php esc_html_e('Partager', 'reussitepersonnelle'); ?></a>
	</div>
</div>
