<?php

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit;
}

// Connecting the required classes
require_once plugin_dir_path( __FILE__ ) . 'includes/class-activation.php';

// Removing the plugin
$activation = new InstantCryptoExchange\Activation();
$activation->delete();