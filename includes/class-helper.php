<?php

namespace InstantCryptoExchange;

// If this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) {
  die;
}

// Class for auxiliary functionality
if ( ! class_exists('Helper') ) {
  class Helper {

    // Getting icon name
    static public function getIconName( $coin_symbol ) {
      return strtolower( $coin_symbol ) . '.png';
    }

    // Getting coin from coins list
    static public function getCoin( $coins_list, $coin_symbol ) {

      $coin_by_symbol = null;

      foreach ( $coins_list as $coin ) {
        if ( $coin_symbol == $coin['symbol'] ) {
          $coin_by_symbol = $coin;
        }
      }

      return $coin_by_symbol;

    }

    // Removing auto p
    static public function removeAutoP( $content ) {

      $order_id = get_query_var('order_id');

      if ( $order_id && API::getOrder($order_id) ) {
        remove_filter( 'the_content', 'wpautop' );
      }

      return $content;

    }

    // Getting status text
    static public function getStatusText( $status ) {

      $status_text = '';

      switch ( $status ) {
        case 'no_deposits': $status_text = 'Awaiting deposit'; break;
        case 'expired': $status_text = 'Expired'; break;
        case 'resolved': $status_text = 'Resolved'; break;
        case 'received': $status_text = 'Awaiting exchange'; break;
        case 'complete': $status_text = 'Completed'; break;
        default: $status_text = 'Failed';
      }

      return $status_text;

    }

    // Getting locale filename
    static public function getLocaleFilename() {

      $wp_locale = get_locale();

      $locale = strstr( $wp_locale, '_', true );
      if ( ! $locale ) {
        $locale = $wp_locale;
      }

      return $locale .= '.js';

    }

    // Formatting number
    static public function formatNumber( $number ) {
      return number_format_i18n( $number, strlen( substr( strrchr($number, '.' ), 1) ) );
    }

    // Getting available coins list
    static public function getAvailableCoinsList() {

      $coins_list = API::getCoinsList();
      if ( ! $coins_list ) {
        $coins_list = [];
      }

      $available_coins_list = [];
      foreach ( $coins_list as $coin ) {
        if ( 'available' === $coin['status'] ) {
          $available_coins_list[] = $coin;
        }
      }

      return json_encode( $available_coins_list );

    }

    public static function httpRequest($url, $decodeJson = TRUE) {
      $response = wp_remote_get($url);
      // check if there is no error in the HTTP request / response
      if (!$response instanceof \WP_Error && isset($response['body'])) {
        return $decodeJson ? json_decode($response['body'], TRUE) : $response['body'];
      } else {
        // die($response->get_error_message());
        return FALSE;
      }
    }

    public static function getVersion($file) {
      $fileData = get_file_data($file, ['Version' => 'Version']);
      return preg_match('#^(\d+\.\d+\.\d+).+#', $fileData['Version'], $matches) && isset($matches[1]) ? $matches[1] : NULL;
    }
  }
}
