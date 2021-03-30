<?php

namespace InstantCryptoExchange;

// If this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) {
  die;
}

// Class for handling AJAX requests
if ( ! class_exists('AJAX') ) {

  class AJAX {

    public function __construct() {
      if( defined('DOING_AJAX') ){

        add_action( 'wp_ajax_load_pair_info', [$this, 'loadPairInfo'] );
        add_action( 'wp_ajax_nopriv_load_pair_info', [$this, 'loadPairInfo'] );

        add_action( 'wp_ajax_load_step2', [$this, 'loadStep2'] );
        add_action( 'wp_ajax_nopriv_load_step2', [$this, 'loadStep2'] );
        add_action( 'wp_ajax_back_to_step1', [$this, 'backToStep1'] );
        add_action( 'wp_ajax_nopriv_back_to_step1', [$this, 'backToStep1'] );

        add_action( 'wp_ajax_create_quick_order', [$this, 'createQuickOrder'] );
        add_action( 'wp_ajax_nopriv_create_quick_order', [$this, 'createQuickOrder'] );
        add_action( 'wp_ajax_create_precise_order', [$this, 'createPreciseOrder'] );
        add_action( 'wp_ajax_nopriv_create_precise_order', [$this, 'createPreciseOrder'] );
        add_action( 'wp_ajax_update_order', [$this, 'updateOrder'] );
        add_action( 'wp_ajax_nopriv_update_order', [$this, 'updateOrder'] );

        add_action( 'wp_ajax_load_order_info', [$this, 'loadOrderInfo'] );
        add_action( 'wp_ajax_nopriv_load_order_info', [$this, 'loadOrderInfo'] );

        add_action( 'wp_ajax_update_transactions_list', [$this, 'updateTransactionsList'] );
        add_action( 'wp_ajax_nopriv_update_transactions_list', [$this, 'updateTransactionsList'] );

      }
    }

    // Loading pair info
    public function loadPairInfo() {

      check_ajax_referer( 'ajaxce-nonce', 'nonce_code' );

      if ( ! isset($_POST['from']) || ! isset($_POST['to']) ) {
        echo 0;
        wp_die();
      }

      $from_symbol = trim( sanitize_text_field($_POST['from']) );
      $to_symbol = trim( sanitize_text_field($_POST['to']) );

      $pair_info = API::getPairInfo( $from_symbol, $to_symbol );
      if ( ! $pair_info ) {
        echo 0;
        wp_die();
      }

      echo json_encode( $pair_info );
      wp_die();

    }

    // Loading step 2
    public function loadStep2() {

      check_ajax_referer( 'ajaxce-nonce', 'nonce_code' );

      if ( ! isset($_POST['color']) || ! isset($_POST['bg']) || ! isset($_POST['method']) || empty($_POST['method']) ) {
        echo 0;
        wp_die();
      }

      $ce_option = get_option('ce_option');
      $from_symbol = trim( sanitize_text_field($_POST['from']) );
      $to_symbol = trim( sanitize_text_field($_POST['to']) );
      $method = trim( sanitize_text_field($_POST['method']) );
      $bg = trim( sanitize_text_field($_POST['bg']) );
      $color = trim( sanitize_text_field($_POST['color']) );
      $limit = $_POST['limit'];
      $fee = $_POST['minerFee'];
      $minimum = $_POST['minimum'];
      $rate = $_POST['rate'];

      $coins_list = API::getCoinsList();
      if ( ! $coins_list ) {
        echo 0;
        wp_die();
      }

      $from = Helper::getCoin( $coins_list, $from_symbol );
      $to = Helper::getCoin( $coins_list, $to_symbol );

      if ( ! $from || ! $to ) {
        echo 0;
        wp_die();
      }

      $from_icon = CE_URL . 'public/img/coins/' . Helper::getIconName( $from['symbol'] );
      $from_name = $from['name'];
      $to_icon = CE_URL . 'public/img/coins/' . Helper::getIconName( $to['symbol'] );
      $to_name = $to['name'];

      ob_start();
      require_once CE_PATH . 'public/partials/step2.php';
      $step2 = ob_get_clean();

      $step2 = str_replace(
        ['%%limit%%', '%%fee%%', '%%minimum%%', '%%rate%%', '%%from_symbol%%', '%%from_icon%%', '%%from_name%%', '%%to_symbol%%', '%%to_icon%%', '%%to_name%%', '%%method%%', '%%fg%%', '%%bg%%', '%%terms_page_url%%'],
        [$limit, $fee, $minimum, $rate, $from_symbol, $from_icon, $from_name, $to_symbol, $to_icon, $to_name, $method, $color, $bg, isset($ce_option['terms_page_id']) && intval($ce_option['terms_page_id'])>0 ? get_page_link($ce_option['terms_page_id']) : '' ],
        $step2);

      echo $step2;
      wp_die();

    }

    // Back to step 1
    public function backToStep1() {

      check_ajax_referer( 'ajaxce-nonce', 'nonce_code' );

      if ( ! isset($_POST['from']) || ! isset($_POST['to']) || ! isset($_POST['color']) || ! isset($_POST['bg']) || ! isset($_POST['method']) || empty($_POST['method']) ) {
        echo 0;
        wp_die();
      }

      $from_symbol = trim( sanitize_text_field($_POST['from']) );
      $to_symbol = trim( sanitize_text_field($_POST['to']) );
      $method = trim( sanitize_text_field($_POST['method']) );
      $bg = trim( sanitize_text_field($_POST['bg']) );
      $color = trim( sanitize_text_field($_POST['color']) );

      $coins_list = API::getCoinsList();
      if ( ! $coins_list ) {
        echo 0;
        wp_die();
      }

      $from = Helper::getCoin( $coins_list, $from_symbol );
      $to = Helper::getCoin( $coins_list, $to_symbol );
      if ( ! $from || ! $to ) {
        echo 0;
        wp_die();
      }

      $from_icon = CE_URL . 'public/img/coins/' . Helper::getIconName( $from['symbol'] );
      $from_name = $from['name'];
      $to_icon = CE_URL . 'public/img/coins/' . Helper::getIconName( $to['symbol'] );
      $to_name = $to['name'];

      ob_start();
      require_once CE_PATH . 'public/partials/step1.php';
      $step1 = ob_get_clean();

      $step1 = str_replace( ['%%from_symbol%%', '%%from_icon%%', '%%from_name%%', '%%to_symbol%%', '%%to_icon%%', '%%to_name%%', '%%method%%', '%%fg%%', '%%bg%%'], [$from_symbol, $from_icon, $from_name, $to_symbol, $to_icon, $to_name, $method, $color, $bg], $step1 );

      echo $step1;
      wp_die();

    }

    // Creating quick order
    public function createQuickOrder() {

      check_ajax_referer( 'ajaxce-nonce', 'nonce_code' );

      if ( ! isset($_POST['withdrawal']) || ! isset($_POST['returnAddress']) || ! isset($_POST['fromSymbol']) || ! isset($_POST['toSymbol']) || ! isset($_POST['color']) || ! isset($_POST['bg']) ) {
        echo 0;
        wp_die();
      }


      $withdrawal = trim( sanitize_text_field($_POST['withdrawal']) );
      $return_address = trim( sanitize_text_field($_POST['returnAddress']) );
      $from_symbol = trim( sanitize_text_field($_POST['fromSymbol']) );
      $to_symbol = trim( sanitize_text_field($_POST['toSymbol']) );
      $color = trim( sanitize_text_field($_POST['color']) );
      $bg = trim( sanitize_text_field($_POST['bg']) );

      $order_id = API::createQuickOrder( $withdrawal, $return_address, $from_symbol, $to_symbol, $color, $bg );
      if ( ! $order_id ) {
        echo 0;
        wp_die();
      }

      $ce_option = get_option( 'ce_option' );
      $status_slug = $ce_option['status_slug'];

      $redirect_url = home_url( "/{$status_slug}/{$order_id}" );

      echo $redirect_url;
      wp_die();

    }

    // Creating precise order
    public function createPreciseOrder() {

      check_ajax_referer( 'ajaxce-nonce', 'nonce_code' );

      if ( ! isset($_POST['withdrawal']) || ! isset($_POST['returnAddress']) || ! isset($_POST['fromSymbol']) || ! isset($_POST['toSymbol']) || ! isset($_POST['amount']) || ! isset($_POST['color']) || ! isset($_POST['bg']) ) {
        echo 0;
        wp_die();
      }

      $withdrawal = trim( sanitize_text_field($_POST['withdrawal']) );
      $return_address = trim( sanitize_text_field($_POST['returnAddress']) );
      $from_symbol = trim( sanitize_text_field($_POST['fromSymbol']) );
      $to_symbol = trim( sanitize_text_field($_POST['toSymbol']) );
      $amount = trim( sanitize_text_field($_POST['amount']) );
      $color = trim( sanitize_text_field($_POST['color']) );
      $bg = trim( sanitize_text_field($_POST['bg']) );

      $order_id = API::createPreciseOrder( $withdrawal, $return_address, $from_symbol, $to_symbol, $amount, $color, $bg );
      if ( ! $order_id ) {
        echo 0;
        wp_die();
      }

      $ce_option = get_option( 'ce_option' );
      $status_slug = $ce_option['status_slug'];

      $redirect_url = home_url( "/{$status_slug}/{$order_id}" );

      echo $redirect_url;
      wp_die();

    }

    // Updating order
    public function updateOrder() {

      check_ajax_referer( 'ajaxce-nonce', 'nonce_code' );

      if ( ! isset($_POST['orderId']) || ! isset($_POST['status']) ) {
        echo 0;
        wp_die();
      }

      $order_id = trim( sanitize_text_field($_POST['orderId']) );
      $status = trim( sanitize_text_field($_POST['status']) );

      $order = API::getOrder( $order_id );

      if ( ! $order ) {
        echo 0;
        wp_die();
      }

      if ( $status == $order['status'] ) {
        echo 0;
        wp_die();
      }

      $step = CryptoExchange::getOrderStatus( $order, $order_id );
      DB::updateOrder( $order, $order_id );

      echo $step;
      wp_die();

    }

    // Loading order info
    public function loadOrderInfo() {

      if ( ! isset($_POST['orderId']) ) {
        echo 0;
        wp_die();
      }

      $order_id = trim( sanitize_text_field($_POST['orderId']) );

      $order_info = API::getOrder( $order_id );

      if ( ! $order_info ) {
        echo 0;
        wp_die();
      }

      $order_info = json_encode( $order_info, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );

      echo $order_info;
      wp_die();

    }

    // Updating transactions list
    public function updateTransactionsList() {

      if ( ! isset($_POST['count']) || ! isset($_POST['type']) || ! isset($_POST['bg']) || ! isset($_POST['color']) ) {
        echo 0;
        wp_die();
      }

      $count = trim( sanitize_text_field($_POST['count']) );
      $type = trim( sanitize_text_field($_POST['type']) );
      $bg = trim( sanitize_text_field($_POST['bg']) );
      $color = trim( sanitize_text_field($_POST['color']) );

      if ( 'local' == $type ) {
        $transactions = DB::getTransactions( $count );
      } else {
        $transactions = API::getTransactions( $count );
      }

      ob_start();
      require_once CE_PATH . 'public/partials/transactions.php';
      $transactions = ob_get_clean();

      $transactions = str_replace( ['%%bg%%', '%%fg%%', '%%count%%', '%%type%%'], [$bg, $color, $count, $type], $transactions );

      echo $transactions;
      wp_die();

    }

  }

  new AJAX();

}
