<?php
/**
 * Analytics tracking integration.
 *
 * @package ReussitePersonnelleCore
 */

namespace ReussitePersonnelle\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', __NAMESPACE__ . '\\render_ga4_tracking', 20 );

/**
 * Render GA4 tracking for anonymous visitors.
 */
function render_ga4_tracking(): void {
	if ( is_user_logged_in() ) {
		return;
	}

	$measurement_id = (string) apply_filters( 'reussitepersonnelle_ga4_measurement_id', 'G-KFY0DMTM5V' );

	if ( '' === $measurement_id ) {
		return;
	}

	$script_url = add_query_arg(
		'id',
		rawurlencode( $measurement_id ),
		'https://www.googletagmanager.com/gtag/js'
	);
	?>
	<!-- Google tag (gtag.js) -->
	<script async src="<?php echo esc_url( $script_url ); ?>"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', <?php echo wp_json_encode( $measurement_id ); ?>);
	</script>
	<?php
}
