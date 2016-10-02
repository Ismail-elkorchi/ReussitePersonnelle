<?php


// Shows the final purchase total at the bottom of the checkout page

 remove_action( 'edd_purchase_form_before_submit', 'edd_checkout_final_total', 999 );
 add_action( 'edd_purchase_form_before_submit', 'reussitepersonnelle_checkout_final_total', 999 );
function reussitepersonnelle_checkout_final_total() {
?>
<p id="edd_final_total_wrap">
	<strong><?php _e( 'Purchase Total:', 'easy-digital-downloads' ); ?></strong>
	<span class="edd_cart_amount" data-subtotal="<?php echo edd_get_cart_subtotal(); ?>" data-total="<?php echo edd_get_cart_subtotal(); ?>"><?php edd_cart_total(); ?></span>
	<?php echo edd_checkout_button_purchase(); ?>
</p>
<?php
}


// Renders the Checkout Submit section

 remove_action( 'edd_purchase_form_after_cc_form', 'edd_checkout_submit', 9999 );
 add_action( 'edd_purchase_form_after_cc_form', 'reussitepersonnelle_checkout_submit', 9999 );
function reussitepersonnelle_checkout_submit() {
?>
	<fieldset id="edd_purchase_submit">

		<?php edd_checkout_hidden_fields(); ?>

		<?php do_action( 'edd_purchase_form_before_submit' ); ?>

		<?php do_action( 'edd_purchase_form_after_submit' ); ?>

		<?php if ( edd_is_ajax_disabled() ) { ?>
			<p class="edd-cancel"><a href="javascript:history.go(-1)"><?php _e( 'Go back', 'easy-digital-downloads' ); ?></a></p>
		<?php } ?>
	</fieldset>
<?php
}


/**
 * Stripe uses it's own credit card form because the card details are tokenized.
 *
 * We don't want the name attributes to be present on the fields in order to prevent them from getting posted to the server
 */

remove_action( 'edd_stripe_cc_form', 'edds_credit_card_form' );
add_action( 'edd_stripe_cc_form', 'reussitepersonnelle_credit_card_form' );
function reussitepersonnelle_credit_card_form( $echo = true ) {

	global $edd_options;

	ob_start(); ?>

	<?php if ( ! wp_script_is ( 'stripe-js' ) ) : ?>
		<?php edd_stripe_js( true ); ?>
	<?php endif; ?>

	<?php do_action( 'edd_before_cc_fields' ); ?>

	<fieldset id="edd_cc_fields" class="edd-do-validate">
		<span><legend><?php _e( 'Credit Card Info', 'edds' ); ?></legend></span>
		<?php if( is_ssl() ) : ?>
			<div id="edd_secure_site_wrapper">
				<span class="padlock"></span>
				<span><?php _e( 'This is a secure SSL encrypted payment.', 'edds' ); ?></span>
			</div>
		<?php endif; ?>
		<p id="edd-card-number-wrap">
			<label for="card_number" class="edd-label">
				<?php _e( 'Card Number', 'edds' ); ?>
				<span class="edd-required-indicator">*</span>
				<span class="card-type"></span>
			</label>
			<input type="tel" pattern="[0-9]{13,16}" autocomplete="off" <?php if ( isset( $edd_options['stripe_js_fallback'] ) ) { echo 'name="card_number" '; } ?>id="card_number" class="card-number edd-input required" placeholder="<?php _e( 'Card number', 'edds' ); ?>" />
		</p>
		<p id="edd-card-cvc-wrap">
			<label for="card_cvc" class="edd-label">
				<?php _e( 'CVC', 'edds' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<input type="tel" pattern="[0-9]{3,4}" size="4" autocomplete="off" <?php if ( isset( $edd_options['stripe_js_fallback'] ) ) { echo 'name="card_cvc" '; } ?>id="card_cvc" class="card-cvc edd-input required" placeholder="<?php _e( 'Security code', 'edds' ); ?>" />
		</p>
		<p id="edd-card-name-wrap">
			<label for="card_name" class="edd-label">
				<?php _e( 'Name on the Card', 'edds' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<input type="text" autocomplete="off" <?php if ( isset( $edd_options['stripe_js_fallback'] ) ) { echo 'name="card_name" '; } ?>id="card_name" class="card-name edd-input required" placeholder="<?php _e( 'Card name', 'edds' ); ?>" />
		</p>
		<?php do_action( 'edd_before_cc_expiration' ); ?>
		<p class="card-expiration">
			<label for="card_exp_month" class="edd-label">
				<?php _e( 'Expiration (MM/YY)', 'edds' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<select <?php if ( isset( $edd_options['stripe_js_fallback'] ) ) { echo 'name="card_exp_month" '; } ?>id="card_exp_month" class="card-expiry-month edd-select edd-select-small required">
				<?php for( $i = 1; $i <= 12; $i++ ) { echo '<option value="' . $i . '">' . sprintf ('%02d', $i ) . '</option>'; } ?>
			</select>
			<span class="exp-divider"> / </span>
			<select <?php if ( isset( $edd_options['stripe_js_fallback'] ) ) { echo 'name="card_exp_year" '; } ?>id="card_exp_year" class="card-expiry-year edd-select edd-select-small required">
				<?php for( $i = date('Y'); $i <= date('Y') + 30; $i++ ) { echo '<option value="' . $i . '">' . substr( $i, 2 ) . '</option>'; } ?>
			</select>
		</p>
		<?php do_action( 'edd_after_cc_expiration' ); ?>

		<div id="ssl-seal">
			<img src="https://www.reussitepersonnelle.com/wp-content/themes/reussitepersonnelle/images/truste.gif" width="143" height="45" />
			<br />
			<img src="https://www.reussitepersonnelle.com/wp-content/themes/reussitepersonnelle/images/verisign.png" width="120" height="58" />
		</div>

	</fieldset>
	<?php
	do_action( 'edd_after_cc_fields' );

	$form = ob_get_clean();

	if ( false !== $echo ) {
		echo $form;
	}

	return $form;
}
