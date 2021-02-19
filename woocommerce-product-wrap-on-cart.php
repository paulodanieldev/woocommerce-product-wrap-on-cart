<?php

/**
 * Plugin Name: InCuca Tech - Woocommerce product wrap on cart
 * Plugin URI: https://github.com/InCuca/woocommerce-product-wrap-on-cart
 * Description: Adds an option to the cart to check if you want to add a product wrap to your purchase.
 * Author: InCuca Tech
 * Author URI: https://incuca.net
 * Version: 1.0.0
 * Tested up to: 5.5.6
 * License: GNU General Public License v3.0
 *
 * @package Pwc_For_WooCommerce
 */

defined('ABSPATH') or exit;

define( 'WC_PWC_VERSION', '1.0.0' );
define( 'WC_PWC_PLUGIN_FILE', __FILE__ );

if ( ! class_exists( 'WC_PWC' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-wc-pwc.php';
	add_action( 'plugins_loaded', array( 'WC_PWC', 'init' ) );
}