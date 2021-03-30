<?php

namespace InstantCryptoExchange;

// If this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) {
  die;
}

// Class for plugin activation
if ( ! class_exists('Activation') ) {
  class Activation {

    const PLUGIN_NAME = 'Instant Cryptocurrency Exchange';
    const MIN_PHP_VERSION = '5.5.0';

    // Runs when the plugin is activated
    public function activate() {

      $this->checkPHPVersion();
      $this->checkCoinsDir();

      $this->addOptions();
      $this->createOrdersTable();

      API::downloadCoinsIcons();

      CryptoExchange::addRewriteRule();
      flush_rewrite_rules();

    }

    // Runs when the plugin is disabled
    public function deactivate() {

      if ( ! current_user_can('activate_plugins') ) {
        return;
      }

      $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
      check_admin_referer( "deactivate-plugin_{$plugin}" );

      delete_transient( 'ce_flush_rules' );
      flush_rewrite_rules();

    }

    // Runs when the plugin is uninstalled
    public function delete() {

      if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
      }

      $this->deleteOrdersTable();
      delete_option( 'ce_option' );
      delete_option( 'ce_coins_alerts' );
      delete_transient( 'ce_flush_rules' );
      flush_rewrite_rules();

    }

    // Checking the PHP version
    public function checkPHPVersion() {
      if ( version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '<') ) {
        wp_die( sprintf( __('<p>PHP %s+ is required to use <b>%s</b> plugin. You have %s installed.</p>', 'instant-cryptocurrency-exchange'), self::MIN_PHP_VERSION, self::PLUGIN_NAME, PHP_VERSION ), 'Plugin Activation Error', ['response' => 200, 'back_link' => TRUE] );
      }
    }

    // Checking if coins directory is writable
    public function checkCoinsDir() {
      if ( ! is_writable(CE_PATH . 'public/img/coins') ) {
        wp_die( '<p>' . sprintf(__('Folder <b>%s</b> is not writable, please set the permissions accordingly.', 'instant-cryptocurrency-exchange'), CE_PATH . 'public/img/coins') . '</p>', 'Plugin Activation Error', ['response' => 200, 'back_link' => TRUE] );
      }
    }

    // Adding options
    public function addOptions() {

      $options = [
        'affiliate_key'   => '',
        'from'            => 'BTC',
        'to'              => 'ETH',
        'status_slug'     => 'instant-crypto-exchange',
        'scripts_placing' => 'header',
        'background'      => 'white',
      ];

      $coins_alerts = [
        'XRP' => "If you enter a XRP refund address, DO NOT use an address from an exchange or shared wallet that requires a Memo. Only use a refund address from a wallet you control, which doesn't require a Destination Tag.",
        'XMR' => "If you enter a Monero refund address, DO NOT use an address from an exchange or shared wallet that requires a payment ID. Only use a refund address from a wallet you control, which doesn't require a payment ID."
      ];

      add_option( 'ce_option', $options );
      add_option( 'ce_coins_alerts', $coins_alerts );

    }

    // Creating orders table
    public function createOrdersTable() {

      global $wpdb;

      $charset_collate = $wpdb->get_charset_collate();
      $table_name = $wpdb->prefix . 'ce_orders';

      $sql = "CREATE TABLE $table_name (
        id int NOT NULL AUTO_INCREMENT,
        order_id varchar(255) NOT NULL,
        type varchar(20) NOT NULL,
        from_amount decimal(20,8) NOT NULL,
        from_currency varchar(20) NOT NULL,
        to_amount decimal(20,8) NOT NULL,
        to_currency varchar(20) NOT NULL,
        rate decimal(20,8) NOT NULL,
        status varchar(100) NOT NULL,
        created datetime NOT NULL,
        color varchar(20) NOT NULL,
        bg varchar(20) NOT NULL,
        PRIMARY KEY id (id),
        KEY order_id (order_id),
        KEY ix_{$table_name}_status (status),
        KEY ix_{$table_name}_created (created)
      ) $charset_collate;";

      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

      $result = dbDelta( $sql );

      if ( ! $result ) {
        wp_die( __('Error while creating a table. Please, try again.', 'instant-cryptocurrency-exchange'), 'Table Creation Error', ['response' => 200, 'back_link' => TRUE] );
      }

    }

    // Deleting orders table
    public function deleteOrdersTable() {

      global $wpdb;

      $table_name = $wpdb->prefix . 'ce_orders';

      $wpdb->query( "DROP TABLE IF EXISTS $table_name" );

    }

  }
}
