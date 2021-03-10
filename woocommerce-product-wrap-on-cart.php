<?php

/**
 * Plugin Name: InCuca Tech - Woocommerce product wrap on cart
 * Plugin URI: https://github.com/InCuca/woocommerce-product-wrap-on-cart
 * Description: Adiciona uma opção ao carrinho para adicionar uma embalagem como produto fisico, permitindo o controle de estoque e inclusão do mesmo ao pedido final.
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