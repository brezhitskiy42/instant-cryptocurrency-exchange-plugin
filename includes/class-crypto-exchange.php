<?php

namespace InstantCryptoExchange;
use \stdClass;
use \WP_Post;

// If this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) {
  die;
}

// Loading additional functionality
require_once plugin_dir_path( __FILE__ ) . 'class-helper.php';
require_once plugin_dir_path( __FILE__ ) . 'class-api.php';
require_once plugin_dir_path( __FILE__ ) . 'class-db.php';
require_once plugin_dir_path( __FILE__ ) . 'class-activation.php';
require_once plugin_dir_path( __FILE__ ) . 'class-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'class-ajax.php';
require_once plugin_dir_path( __FILE__ ) . 'class-admin.php';
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Main plugin class
if ( ! class_exists('CryptoExchange') ) {
  class CryptoExchange {
    private $version;

    public function __construct() {

      $this->version = Helper::getVersion(CE_PLUGIN_FILE);
      $ce_option = get_option( 'ce_option' );
      $scripts_placing = $ce_option['scripts_placing'];
      $priority = 'footer' == $scripts_placing ? 10 : 0;

      add_action( 'wp_enqueue_scripts', [$this, 'loadStylesScripts'], $priority );
      if ( is_plugin_active('gutenberg/gutenberg.php') ) {
        add_action( 'enqueue_block_editor_assets', [$this, 'loadBlocksStylesScripts'] );
      }

      add_filter( 'query_vars', [$this, 'addQueryVars'] );
      add_action( 'init', [$this, 'addRewriteRule'] );

      add_filter( 'template_redirect', [$this, 'showOrderStatus'] );

      // add_filter( 'the_content', ['InstantCryptoExchange\Helper', 'removeAutoP'], 0 );
      add_filter( 'the_title', [$this, 'removeTitle'] );

      add_action( 'plugins_loaded', [$this, 'loadTextDomain'] );

    }

    // Loading styles and scripts
    public function loadStylesScripts() {

      wp_register_style( 'ce-font-awesome', CE_URL . 'public/vendor/font-awesome/css/font-awesome.min.css' );
      wp_register_style( 'ce-popup', CE_URL . 'public/vendor/magnific-popup/magnific-popup.css' );
      wp_register_style( 'ce-style', CE_URL . 'public/css/style.min.css' );

      $ce_option = get_option( 'ce_option' );
      $scripts_placing = $ce_option['scripts_placing'];
      $in_footer = 'footer' == $scripts_placing ? true : false;

      $locale = Helper::getLocaleFilename();

      wp_register_script( 'ce-validator', CE_URL . 'public/vendor/validator/validator.min.js', [], $this->version, $in_footer );
      wp_register_script( 'ce-popup', CE_URL . 'public/vendor/magnific-popup/jquery.magnific-popup.min.js', ['jquery'], $this->version, $in_footer );
      wp_register_script( 'ce-clipboard', CE_URL . 'public/vendor/clipboard/clipboard.min.js', [], $this->version, $in_footer );
      wp_register_script( 'ce-tippy', CE_URL . 'public/vendor/tippy/tippy.all.min.js', [], $this->version, $in_footer );
      wp_register_script( 'ce-qrious', CE_URL . 'public/vendor/qrious/qrious.min.js', [], $this->version, $in_footer );
      wp_register_script( 'ce-moment', CE_URL . 'public/vendor/moment/moment.min.js', [], $this->version, $in_footer );
      wp_register_script( 'ce-moment-locale', CE_URL . 'public/vendor/moment/locale/' . $locale, [], $this->version, $in_footer );
      wp_register_script( 'ce-tinycolor', CE_URL . 'public/vendor/tinycolor/tinycolor-min.js', [], $this->version, $in_footer );
      wp_register_script( 'ce-main', CE_URL . 'public/js/main'.(file_exists(CE_PATH . 'public/js/main.min.js') ? '.min' : '').'.js', ['jquery'], $this->version, $in_footer );

      wp_enqueue_style( 'ce-font-awesome' );
      wp_enqueue_style( 'ce-popup' );
      wp_enqueue_style( 'ce-style' );

      wp_enqueue_script( 'ce-validator' );
      wp_enqueue_script( 'ce-popup' );
      wp_enqueue_script( 'ce-clipboard' );
      wp_enqueue_script( 'ce-tippy' );
      wp_enqueue_script( 'ce-qrious' );
      wp_enqueue_script( 'ce-moment' );
      if ( file_exists(CE_PATH . 'public/vendor/moment/locale/' . $locale) ) { wp_enqueue_script( 'ce-moment-locale' ); }
      wp_enqueue_script( 'ce-tinycolor' );
      wp_enqueue_script( 'ce-main' );

      wp_localize_script( 'ce-main', 'ajaxce', [ 'url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('ajaxce-nonce') ] );

    }

    // Loading blocks styles and scripts
    public function loadBlocksStylesScripts() {

      wp_register_script( 'ce-block-crypto-exchange', CE_URL . 'public/js/crypto-exchange.min.js', ['wp-blocks', 'wp-i18n', 'wp-element'] );

      wp_enqueue_script( 'ce-block-crypto-exchange' );

      wp_localize_script( 'ce-block-crypto-exchange', 'cryptoExchange', [
        'coinsList' => Helper::getAvailableCoinsList(),
        'cryptocurrencyExchange' => __('Instant Crypto Exchange', 'instant-cryptocurrency-exchange'),
        'cryptocurrencyExchangeForm' => __('Crypto exchange form', 'instant-cryptocurrency-exchange'),
        'coinsSettings' => __('Coins Settings', 'instant-cryptocurrency-exchange'),
        'colorSettings' => __('Color Settings', 'instant-cryptocurrency-exchange'),
        'fromCoin' => __('Default coin (from)', 'instant-cryptocurrency-exchange'),
        'toCoin' => __('Default coin (to)', 'instant-cryptocurrency-exchange'),
        'foregroundColor' => __('Foreground Color', 'instant-cryptocurrency-exchange'),
        'backgroundColor' => __('Background Color', 'instant-cryptocurrency-exchange'),
        'exchangeSubtext' => __('The exchange form will be fully displayed only when viewing the page', 'instant-cryptocurrency-exchange'),
        'cryptoExchangeTransactions' => __('Crypto exchange transactions', 'instant-cryptocurrency-exchange'),
        'transactionsSettings' => __('Transactions Settings', 'instant-cryptocurrency-exchange'),
        'maxNumber' => __('Max number of transactions', 'instant-cryptocurrency-exchange'),
        'transactions' => __('Transactions', 'instant-cryptocurrency-exchange'),
        'all' => __('All', 'instant-cryptocurrency-exchange'),
        'thisWebsiteOnly' => __('This website only', 'instant-cryptocurrency-exchange'),
        'transactionsSubtext' => __('Transactions will be fully displayed only when viewing the page', 'instant-cryptocurrency-exchange'),
      ] );

    }

    // Adding query vars
    public function addQueryVars( $vars ) {
      $vars[] = 'status';
      $vars[] = 'order_id';
      return $vars;
    }

    // Adding rewrite rule
    public function addRewriteRule() {

      $ce_option = get_option( 'ce_option' );
      $status_slug = $ce_option['status_slug'];

      add_rewrite_rule( '^(' . $status_slug . ')/([^/]*)/?', 'index.php?status=$matches[1]&order_id=$matches[2]', 'top' );

    }

    // Showing order status page
    public function showOrderStatus( $order_id ) {

      global $wp, $wp_query;

      $order_id = get_query_var('order_id');
      if ( ! $order_id ) {
        return;
      }

      $order = API::getOrder( $order_id, true );
      if ( ! $order ) {
        return;
      }

      $content = self::getFakePostContent( $order, $order_id );

      $post_id = -999;
      $post = new stdClass();
      $post->ID = $post_id;
      $post->post_author = 1;
      $post->post_date = current_time( 'mysql' );
      $post->post_date_gmt = current_time( 'mysql', 1 );
      $post->post_title = __( 'Order ID', 'instant-cryptocurrency-exchange' ) . ": {$order_id}";
      $post->post_content = $content;
      $post->comment_status = 'closed';
      $post->ping_status = 'closed';
      $post->post_name = $order_id;
      $post->post_type = 'page';
      $post->filter = 'raw';

      $wp_post = new WP_Post( $post );
      wp_cache_add( $post_id, $wp_post, 'posts' );

      $wp_query->post = $wp_post;
      $wp_query->posts = [ $wp_post ];
      $wp_query->queried_object = $wp_post;
      $wp_query->queried_object_id = $post_id;
      $wp_query->found_posts = 1;
      $wp_query->post_count = 1;
      $wp_query->max_num_pages = 1;
      $wp_query->is_page = true;
      $wp_query->is_singular = true;
      $wp_query->is_single = false;
      $wp_query->is_attachment = false;
      $wp_query->is_archive = false;
      $wp_query->is_category = false;
      $wp_query->is_tag = false;
      $wp_query->is_tax = false;
      $wp_query->is_author = false;
      $wp_query->is_date = false;
      $wp_query->is_year = false;
      $wp_query->is_month = false;
      $wp_query->is_day = false;
      $wp_query->is_time = false;
      $wp_query->is_search = false;
      $wp_query->is_feed = false;
      $wp_query->is_comment_feed = false;
      $wp_query->is_trackback = false;
      $wp_query->is_home = false;
      $wp_query->is_embed = false;
      $wp_query->is_404 = false;
      $wp_query->is_paged = false;
      $wp_query->is_admin = false;
      $wp_query->is_preview = false;
      $wp_query->is_robots = false;
      $wp_query->is_posts_page = false;
      $wp_query->is_post_type_archive = false;

      $GLOBALS['wp_query'] = $wp_query;
      $wp->register_globals();

    }

    // Getting fake post content
    static public function getFakePostContent( $order, $order_id ) {

      $step = self::getOrderStatus( $order, $order_id );

      $current_url = ( isset($_SERVER['HTTPS']) ? 'https' : 'http' ) . "://{$_SERVER[HTTP_HOST]}{$_SERVER[REQUEST_URI]}";

      ob_start();
      require_once CE_PATH . 'public/partials/bookmark-popup.php';
      $bookmark_popup = ob_get_clean();

      return $step . $bookmark_popup;

    }

    // Getting order status content
    static public function getOrderStatus( $order, $order_id ) {

      $status = $order['status'];
      $step = '';

      $colors = DB::getColors( $order_id );
      $bg = $colors['bg'];
      $color = $colors['color'];

      $from_symbol = $order['incomingType'];
      $to_symbol = $order['outgoingType'];
      $pair_info = API::getPairInfo( $from_symbol, $to_symbol );

      if ( 'no_deposits' == $status || 'expired' == $status || 'resolved' == $status ) {

        $deposit = $order['deposit'];
        $amount = $order['incomingCoin'];
        $state_class = 'no_deposits' == $status ? 'active__ce' : 'error__ce';

        ob_start();
        require_once CE_PATH . 'public/partials/step3.php';
        $step = ob_get_clean();

      } else if ( 'received' == $status ) {

        $amount = $order['incomingCoin'];
        $rate = DB::getRate( $order_id );

        ob_start();
        require_once CE_PATH . 'public/partials/step4.php';
        $step = ob_get_clean();

      } else if ( 'complete' == $status ) {

        $amount = $order['outgoingCoin'];
        $to_name = $order['outgoingCoinInfo']['name'];
        $blockchain_link = $order['transactionURL'];

        ob_start();
        require_once CE_PATH . 'public/partials/step5.php';
        $step = ob_get_clean();

      }

      return $step;

    }

    // Loading text domain
    public function loadTextDomain() {
      load_plugin_textdomain( 'instant-cryptocurrency-exchange', false, 'instant-cryptocurrency-exchange/lang' );
    }

    // Removing title
    public function removeTitle( $title ) {

      $order_id = get_query_var('order_id');

      if ( $order_id && API::getOrder($order_id) ) {
        return;
      }

      return $title;

    }

  }
}
