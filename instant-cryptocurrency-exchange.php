<?php
/**
 * Plugin Name: Cryptocurrency Exchange
 * Description: This plugin allows visitors of your website to instantly exchange cryptocurrencies using ShapeShift API.
 * Version: 2.0.0
 * Text Domain: instant-cryptocurrency-exchange
 * Domain Path: /lang
 */

// If this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) {
  die;
}

// Paths to the plugin root directory
define( 'CE_PATH',  plugin_dir_path( __FILE__ ) );
define( 'CE_URL',  plugin_dir_url( __FILE__ ) );
// path to the main plugin file
define( 'CE_PLUGIN_FILE',  __FILE__ );

// Connecting the main class
require_once plugin_dir_path( __FILE__ ) . 'includes/class-crypto-exchange.php';

// Activating the plugin
function ce_activate_crypto_exchange() {
  $activation = new InstantCryptoExchange\Activation();
  $activation->activate();
}

// Deactivating the plugin
function ce_deactivate_crypto_exchange() {
  $activation = new InstantCryptoExchange\Activation();
  $activation->deactivate();
}

register_activation_hook( __FILE__, 'ce_activate_crypto_exchange' );
register_deactivation_hook( __FILE__, 'ce_deactivate_crypto_exchange' );

// Running the plugin
new InstantCryptoExchange\CryptoExchange();