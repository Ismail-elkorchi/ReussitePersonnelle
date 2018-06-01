<?php

// Enqueue Woocommerce style
add_action( 'wp_enqueue_scripts', 'reussitepersonnelle_enqueue_woocommerce_style' );
function reussitepersonnelle_enqueue_woocommerce_style(){
	wp_register_style( 'reussitepersonnelle-woocommerce', get_stylesheet_directory_uri() . '/lib/woocommerce/woocommerce.css' );
	wp_enqueue_style( 'reussitepersonnelle-woocommerce' );
}

// Remove WooCommerce Generator tag, styles, and scripts from non WooCommerce pages.
add_action( 'wp_enqueue_scripts', 'reussitepersonnelle_dequeue_woocommerce_styles_scripts', 99 );
function reussitepersonnelle_dequeue_woocommerce_styles_scripts() {

	// Remove generator meta tag
	remove_action('wp_head','wc_generator_tag');

	// First check that woo exists to prevent fatal errors
	if ( function_exists( 'is_woocommerce' ) ) {
		// Dequeue scripts and styles
		if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
			wp_dequeue_style( 'woocommerce-general' );
			wp_dequeue_style( 'woocommerce-layout' );
			wp_dequeue_style( 'woocommerce-smallscreen' );
			wp_dequeue_style( 'woocommerce_frontend_styles' );
			wp_dequeue_style( 'woocommerce_fancybox_styles' );
			wp_dequeue_style( 'woocommerce_chosen_styles' );
			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
			wp_dequeue_style( 'reussitepersonnelle-woocommerce' );
			wp_dequeue_script( 'wc_price_slider' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-add-to-cart' );
			wp_dequeue_script( 'wc-cart-fragments' );
			wp_dequeue_script( 'wc-checkout' );
			wp_dequeue_script( 'wc-add-to-cart-variation' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-cart' );
			wp_dequeue_script( 'wc-chosen' );
			wp_dequeue_script( 'woocommerce' );
			wp_dequeue_script( 'prettyPhoto' );
			wp_dequeue_script( 'prettyPhoto-init' );
			wp_dequeue_script( 'jquery-blockui' );
			wp_dequeue_script( 'jquery-placeholder' );
			wp_dequeue_script( 'fancybox' );
			wp_dequeue_script( 'jqueryui' );
		}
	}
}

// Remove Product Permalink from order table
add_filter( 'woocommerce_order_item_name', 'reussitepersonnelle_remove_permalink_order_table', 10, 3 );
function reussitepersonnelle_remove_permalink_order_table( $name, $item, $order ) {
   $name = $item['name'];
   return $name . ':';
}

// Remove quantity from order table
add_filter( 'woocommerce_order_item_quantity_html', '__return_false', 10, 2 );

// Empty cart when product is added to cart, so we can't have multiple products in cart
add_action( 'woocommerce_add_cart_item_data', 'reussitepersonnelle_clear_cart' );
function reussitepersonnelle_clear_cart() {
  wc_empty_cart();
}

// Skip the cart and redirect to checkout url when clicking on Add to cart
add_filter( 'woocommerce_add_to_cart_redirect', 'reussitepersonnelle_add_to_cart_redirect');
function reussitepersonnelle_add_to_cart_redirect() {
  // Remove messages
  wc_clear_notices();

  return wc_get_checkout_url();
}

// Global redirect to checkout when hitting cart page
add_action( 'template_redirect', 'reussitepersonnelle_cart_redirect' );
function reussitepersonnelle_cart_redirect() {

  if ( ! is_cart() ) {
    return;
  }

  // Redirect to checkout page
  wp_redirect( wc_get_checkout_url(), '301' );
  exit;

}

// Remove unnecessary checkout fields
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
add_filter( 'woocommerce_checkout_fields' , 'reussitepersonnelle_override_checkout_fields' );
function reussitepersonnelle_override_checkout_fields( $fields ) {

  unset($fields['billing']['billing_company']);
  unset($fields['billing']['billing_address_1']);
  unset($fields['billing']['billing_address_2']);
  unset($fields['billing']['billing_city']);
  unset($fields['billing']['billing_postcode']);
  unset($fields['billing']['billing_country']);
  unset($fields['billing']['billing_state']);
  unset($fields['billing']['billing_phone']);

  return $fields;
}

// Unset all options related to the cart
$WooCartOptions = get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' || get_option( 'woocommerce_enable_ajax_add_to_cart' ) == 'yes';
if ( $WooCartOptions ) {
  update_option( 'woocommerce_cart_redirect_after_add', 'no' );
  update_option( 'woocommerce_enable_ajax_add_to_cart', 'no' );
}

// Show Apple Pay on checkout page
add_filter( 'wc_stripe_show_payment_request_on_checkout', '__return_true' );

?>
