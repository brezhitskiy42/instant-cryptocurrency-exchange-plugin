<?php

namespace InstantCryptoExchange;

// If this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) {
  die;
}

// Class for adding functionality to the admin panel
if ( ! class_exists('Admin') ) {

  class Admin {
    private $version;

    public function __construct() {

      $this->version = Helper::getVersion(CE_PLUGIN_FILE);
      $ce_option = get_option( 'ce_option' );
      $scripts_placing = $ce_option['scripts_placing'];
      $priority = 'footer' == $scripts_placing ? 10 : 0;

      add_action( 'admin_enqueue_scripts', [ $this, 'loadStylesScripts' ], $priority );
      add_action( 'admin_menu', [$this, 'addMenuPages'] );
      add_action( 'admin_init', [$this, 'registerSettings'] );
      add_filter( 'plugin_action_links_instant-cryptocurrency-exchange/instant-cryptocurrency-exchange.php', [$this, 'addPluginActionLinks'] );

    }

    // Loading styles and scripts
    public function loadStylesScripts() {

      wp_register_style( 'ce-font-awesome', CE_URL . 'public/vendor/font-awesome/css/font-awesome.min.css' );
      wp_register_style( 'ce-select2', CE_URL . 'public/vendor/select2/css/select2.min.css' );
      wp_register_style( 'ce-dataTables', CE_URL . 'public/vendor/datatables/css/jquery.dataTables.css' );
      wp_register_style( 'ce-dataTables-responsive', CE_URL . 'public/vendor/datatables/css/responsive.dataTables.min.css' );
      wp_register_style( 'ce-popup', CE_URL . 'public/vendor/magnific-popup/magnific-popup.css' );
      wp_register_style( 'ce-prism', CE_URL . 'public/vendor/prism/prism.css' );
      wp_register_style( 'ce-style', CE_URL . 'public/css/admin.min.css' );

      $ce_option = get_option( 'ce_option' );
      $scripts_placing = $ce_option['scripts_placing'];
      $in_footer = 'footer' == $scripts_placing ? true : false;

      wp_register_script( 'ce-select2', CE_URL . 'public/vendor/select2/js/select2.min.js', ['jquery'], $this->version, $in_footer );
      wp_register_script( 'ce-dataTables', CE_URL . 'public/vendor/datatables/js/jquery.dataTables.min.js', ['jquery'], $this->version, $in_footer );
      wp_register_script( 'ce-dataTables-responsive', CE_URL . 'public/vendor/datatables/js/dataTables.responsive.min.js', ['jquery'], $this->version, $in_footer );
      wp_register_script( 'ce-popup', CE_URL . 'public/vendor/magnific-popup/jquery.magnific-popup.min.js', ['jquery'], $this->version, $in_footer );
      wp_register_script( 'ce-prism', CE_URL . 'public/vendor/prism/prism.js', [], $this->version, $in_footer );
      wp_register_script( 'ce-admin', CE_URL . 'public/js/admin'.(file_exists(CE_PATH . 'public/js/admin.min.js') ? '.min' : '').'.js', ['jquery'], $this->version, $in_footer );

      wp_enqueue_style( 'ce-font-awesome' );
      wp_enqueue_style( 'ce-select2' );
      wp_enqueue_style( 'ce-dataTables' );
      wp_enqueue_style( 'ce-dataTables-responsive' );
      wp_enqueue_style( 'ce-popup' );
      wp_enqueue_style( 'ce-prism' );
      wp_enqueue_style( 'ce-style' );

      wp_enqueue_script( 'ce-select2' );
      wp_enqueue_script( 'ce-dataTables' );
      wp_enqueue_script( 'ce-dataTables-responsive' );
      wp_enqueue_script( 'ce-popup' );
      wp_enqueue_script( 'ce-prism' );
      wp_enqueue_script( 'ce-admin' );

    }

    // Registering pages in the admin panel
    public function addMenuPages() {

      add_menu_page( __('Instant Crypto Exchange', 'instant-cryptocurrency-exchange'), __('Instant Crypto Exchange', 'instant-cryptocurrency-exchange'), 'manage_options', 'ce-manage-transactions', [$this, 'showTransactionsListPage'], CE_URL . 'public/img/icon.png' );
      $id1 = add_submenu_page( 'ce-manage-transactions', __('Transactions', 'instant-cryptocurrency-exchange'), '<i class="fa fa-exchange"></i> ' . __('Transactions', 'instant-cryptocurrency-exchange'), 'manage_options', 'ce-manage-transactions', [$this, 'showTransactionsListPage'] );
      $id2 = add_submenu_page( 'ce-manage-transactions', __('Settings', 'instant-cryptocurrency-exchange'), '<i class="fa fa-cogs"></i> ' . __('Settings', 'instant-cryptocurrency-exchange'), 'manage_options', 'ce-settings', [$this, 'showSettingsPage'] );
    }

    // Settings registering
    public function registerSettings() {

      if ( delete_transient('ce_flush_rules') ) {
        flush_rewrite_rules();
      }

      add_settings_section( 'ce_main_section', __('Main settings', 'instant-cryptocurrency-exchange'), '', 'ce-settings' );
      add_settings_section( 'ce_coins_alerts_section', __('Coins warnings', 'instant-cryptocurrency-exchange'), '', 'ce-settings' );

      register_setting( 'ce_option_group', 'ce_option', [$this, 'sanitizeOptions'] );
      register_setting( 'ce_option_group', 'ce_coins_alerts', [$this, 'sanitizeCoinsAlerts'] );

      add_settings_field( 'affiliate_key', __('ShapeShift affiliate key', 'instant-cryptocurrency-exchange'), [$this, 'fillAffiliateKeyField'], 'ce-settings', 'ce_main_section' );
      add_settings_field( 'from', __('Default coin (from)', 'instant-cryptocurrency-exchange'), [$this, 'fillFromField'], 'ce-settings', 'ce_main_section' );
      add_settings_field( 'to', __('Default coin (to)', 'instant-cryptocurrency-exchange'), [$this, 'fillToField'], 'ce-settings', 'ce_main_section' );
      add_settings_field( 'background', __('Background', 'instant-cryptocurrency-exchange'), [$this, 'fillBackgroundField'], 'ce-settings', 'ce_main_section' );
      add_settings_field( 'status_slug', __('Order page slug', 'instant-cryptocurrency-exchange'), [$this, 'fillStatusSlugField'], 'ce-settings', 'ce_main_section' );
      add_settings_field( 'terms_page_id', __('Terms and Conditions page', 'instant-cryptocurrency-exchange'), [$this, 'fillTermsPageField'], 'ce-settings', 'ce_main_section' );
      add_settings_field( 'scripts_placing', __('Enqueue scripts in', 'instant-cryptocurrency-exchange'), [$this, 'fillScriptsPlacingField'], 'ce-settings', 'ce_main_section' );

      add_settings_field( 'coins_alerts', __('Coin alert', 'instant-cryptocurrency-exchange'), [$this, 'fillCoinsAlertsFields'], 'ce-settings', 'ce_coins_alerts_section' );

    }

    // Showing transactions list page
    public function showTransactionsListPage() {

      $orders = DB::getOrders();

      ob_start();
      require_once CE_PATH . 'public/partials/admin/transactions-list-page.php';
      echo ob_get_clean();

    }

    // Showing settings page
    public function showSettingsPage() {

      ob_start();
      require_once CE_PATH . 'public/partials/admin/settings-page.php';
      echo ob_get_clean();

    }

    // From coin output
    public function fillFromField() {

      ob_start();
      require_once CE_PATH . 'public/partials/admin/from-field.php';
      echo ob_get_clean();

    }

    // To coin output
    public function fillToField() {

      ob_start();
      require_once CE_PATH . 'public/partials/admin/to-field.php';
      echo ob_get_clean();

    }

    // Status slug output
    public function fillStatusSlugField() {

      $ce_option = get_option( 'ce_option' );
      $status_slug = $ce_option['status_slug'];

      ob_start();
      require_once CE_PATH . 'public/partials/admin/status-slug-field.php';
      $status_slug_field = ob_get_clean();

      echo str_replace( '%%status_slug%%', $status_slug, $status_slug_field );

    }

    // Affiliate key output
    public function fillAffiliateKeyField() {

      $ce_option = get_option( 'ce_option' );
      $affiliate_key = $ce_option['affiliate_key'];

      ob_start();
      require_once CE_PATH . 'public/partials/admin/affiliate-key-field.php';
      $affiliate_key_field = ob_get_clean();

      echo str_replace( '%%affiliate_key%%', $affiliate_key, $affiliate_key_field );

    }

    // Scripts placing output
    public function fillScriptsPlacingField() {

      ob_start();
      require_once CE_PATH . 'public/partials/admin/scripts-placing-field.php';
      echo ob_get_clean();

    }

    // Background output
    public function fillBackgroundField() {

      ob_start();
      require_once CE_PATH . 'public/partials/admin/background-field.php';
      echo ob_get_clean();

    }

    // Terms page output
    public function fillTermsPageField() {

      ob_start();
      require_once CE_PATH . 'public/partials/admin/terms-page-field.php';
      echo ob_get_clean();

    }

    // Coins alerts output
    public function fillCoinsAlertsFields() {

      ob_start();
      require_once CE_PATH . 'public/partials/admin/coins-alerts-fields.php';
      echo ob_get_clean();

    }

    // Checking the transferred settings
    public function sanitizeOptions( $value ) {

      set_transient( 'ce_flush_rules', 1 );

      $ce_option = get_option( 'ce_option' );

      $from = $value['from'];
      $to = $value['to'];
      $status_slug = $value['status_slug'];
      $scripts_placing = $value['scripts_placing'];

      if ( empty($from) || empty($to) || empty($status_slug) || empty($scripts_placing) ) {

        add_settings_error( 'ce_option', 'ce-option', __('Empty value.', 'instant-cryptocurrency-exchange') );

        return $ce_option;

      }

      return $value;

    }

    // Checking the transferred coins alerts
    public function sanitizeCoinsAlerts( $value ) {

      if ( ! $value ) {
        return [];
      }

      $ce_coins_alerts = get_option( 'ce_coins_alerts' );

      $error = false;
      foreach ( $value as $coin => $alert ) {
        if ( '' == $alert ) {
          $error = true;
          break;
        }
      }

      if ( $error ) {

        add_settings_error( 'ce_coins_alerts', 'ce-coins-alerts', __('Empty value.', 'instant-cryptocurrency-exchange') );

        return $ce_coins_alerts;

      }

      return $value;

    }

    // Adding plugin action links
    public function addPluginActionLinks($links) {    
      return $links;
    }
  }

  new Admin();

}
