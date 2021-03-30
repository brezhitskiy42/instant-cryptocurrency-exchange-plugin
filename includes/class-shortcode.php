<?php

namespace InstantCryptoExchange;

// If this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) {
  die;
}

// Class for creating and adding a shortcode
if ( ! class_exists('Shortcode') ) {

  class Shortcode {

    // Shortcode adding
    public function __construct() {
      add_shortcode( 'instant_crypto_exchange', [ $this, 'createMainShortcode' ] );
      add_shortcode( 'instant_crypto_exchange_transactions', [ $this, 'createTransactionsShortcode' ] );
    }

    // Creating main shortcode
    public function createMainShortcode($params) {

      $ce_option = get_option( 'ce_option' );
      $background_hex = ( 'white' === $ce_option['background'] ) ? '#ffffff' : '#000000';

      $from_symbol = isset($params['from']) ? $params['from'] : $ce_option['from'];
      $to_symbol = isset($params['to']) ? $params['to'] : $ce_option['to'];
      $fg = isset($params['foreground']) ? $params['foreground'] : '#55779f';
      $bg = isset($params['background']) ? $params['background'] : $background_hex;

      API::downloadCoinsIcons();

      $coins_list = API::getCoinsList();
      if ( ! $coins_list ) {
        return;
      }

      $from = Helper::getCoin( $coins_list, $from_symbol );
      $to = Helper::getCoin( $coins_list, $to_symbol );
      if ( ! $from || ! $to ) {
        return;
      }

      $from_icon = CE_URL . 'public/img/coins/' . Helper::getIconName( $from['symbol'] );
      $from_name = $from['name'];
      $to_icon = CE_URL . 'public/img/coins/' . Helper::getIconName( $to['symbol'] );
      $to_name = $to['name'];
      $method = 'quick';

      ob_start();
      require_once CE_PATH . 'public/partials/step1.php';
      $step1 = ob_get_clean();

      $step1 = str_replace(
        ['%%from_symbol%%', '%%from_icon%%', '%%from_name%%', '%%to_symbol%%', '%%to_icon%%', '%%to_name%%', '%%method%%', '%%bg%%', '%%fg%%'],
        [$from_symbol, $from_icon, $from_name, $to_symbol, $to_icon, $to_name, $method, $bg, $fg],
        $step1
      );

      $coins_available = [];
      $coins_unavailable = [];
      foreach ( $coins_list as $coin ) {
        if ( 'available' === $coin['status'] ) {
          $coins_available[] = $coin;
        } else {
          $coins_unavailable[] = $coin;
        }
      }
      $coins_alerts = get_option( 'ce_coins_alerts' );

      ob_start();
      require_once CE_PATH . 'public/partials/coins-popup.php';
      $coins_popup = ob_get_clean();

      ob_start();
      require_once CE_PATH . 'public/partials/alert-popup.php';
      $alert_popup = ob_get_clean();

      return $step1 . $coins_popup . $alert_popup;

    }

    // Creating transactions shortcode
    public function createTransactionsShortcode( $atts ) {

      $ce_option = get_option( 'ce_option' );
      $background_hex = ( 'white' === $ce_option['background'] ) ? '#ffffff' : '#000000';

      $count = isset($atts['count']) ? $atts['count'] : 10;
      $type = isset($atts['type']) ? $atts['type'] : 'global';
      $fg = isset($atts['foreground']) ? $atts['foreground'] : '#55779f';
      $bg = isset($atts['background']) ? $atts['background'] : $background_hex;

      if ( 'local' == $type ) {
        $transactions = DB::getTransactions( $count );
      } else {
        $transactions = API::getTransactions( $count );
      }

      ob_start();
      require_once CE_PATH . 'public/partials/transactions.php';
      $transactions = ob_get_clean();

      $transactions = str_replace( ['%%bg%%', '%%fg%%', '%%count%%', '%%type%%'], [$bg, $fg, $count, $type], $transactions );

      return $transactions;

    }

  }

  new Shortcode();

}
