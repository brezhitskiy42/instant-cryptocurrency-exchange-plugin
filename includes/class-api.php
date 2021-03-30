<?php

namespace InstantCryptoExchange;

// If this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) {
  die;
}

// Class for handling ShapeShift API
if ( ! class_exists('API') ) {
  class API {

    // Getting coins list
    static public function getCoinsList() {

      $coins_list_url = 'https://shapeshift.io/getcoins';
      $coins_list = Helper::httpRequest( $coins_list_url );

      if ( ! $coins_list || array_key_exists('error', $coins_list) ) {
        return false;
      }

      return $coins_list;

    }

    // Getting pair info
    static public function getPairInfo( $from, $to ) {

      $pair = strtolower( "{$from}_{$to}" );
      $pair_info_url = "https://shapeshift.io/marketinfo/{$pair}";
      $pair_info = Helper::httpRequest( $pair_info_url );

      if ( ! $pair_info || array_key_exists('error', $pair_info) ) {
        return false;
      }

      return $pair_info;

    }

    // Downloading coins icons
    static public function downloadCoinsIcons() {

      $coins_list = self::getCoinsList();

      if ( ! $coins_list ) {
        return;
      }

      $coins_dir_path = CE_PATH . 'public/img/coins/';
      foreach ( $coins_list as $coin ) {

        $icon_name = Helper::getIconName( $coin['symbol'] );

        // download icon only if it doesn't exist
        if (!file_exists($coins_dir_path . $icon_name))
          file_put_contents( $coins_dir_path . $icon_name, file_get_contents($coin['image']) );
      }
    }

    // Creating quick order
    static public function createQuickOrder( $withdrawal, $return_address, $from_symbol, $to_symbol, $color, $bg ) {

      if ( ! $withdrawal || ! $return_address || ! $from_symbol || ! $to_symbol || ! $color || ! $bg ) {
        return false;
      }

      $ce_option = get_option( 'ce_option' );
      $pair = strtolower( "{$from_symbol}_{$to_symbol}" );
      $url = 'https://shapeshift.io/shift';
      $data = [ 'withdrawal' => $withdrawal, 'pair' => $pair, 'returnAddress' => $return_address ];
      if (isset($ce_option['affiliate_key']) && $ce_option['affiliate_key']!='')
        $data['apiKey'] = $ce_option['affiliate_key'];

      $resp = wp_remote_post( $url, [
        'timeout' => 45,
        'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
        'body' => json_encode($data)
      ] );

      if ( is_wp_error( $resp ) || wp_remote_retrieve_response_code( $resp ) !== 200 ) {
        return false;
      }

      $body = wp_remote_retrieve_body( $resp );
      $resp_data = json_decode( $body, true );

      if ( array_key_exists('error', $resp_data) ) {
        return false;
      }

      $order_id = $resp_data['orderId'];

      $insert_result = DB::createOrder( $order_id, 'quick', $color, $bg );

      if ( ! $insert_result ) {
        return false;
      }

      return $order_id;

    }

    // Creating precise order
    static public function createPreciseOrder( $withdrawal, $return_address, $from_symbol, $to_symbol, $amount, $color, $bg ) {

      if ( ! $withdrawal || ! $return_address || ! $from_symbol || ! $to_symbol || ! $amount || ! $color || ! $bg ) {
        return false;
      }

      $ce_option = get_option( 'ce_option' );
      $pair = strtolower( "{$from_symbol}_{$to_symbol}" );
      $url = 'https://shapeshift.io/sendamount';
      $data = [ 'amount' => $amount, 'withdrawal' => $withdrawal, 'pair' => $pair, 'returnAddress' => $return_address ];
      if (isset($ce_option['affiliate_key']) && $ce_option['affiliate_key']!='')
        $data['apiKey'] = $ce_option['affiliate_key'];

      $resp = wp_remote_post( $url, [
        'timeout' => 45,
        'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
        'body' => json_encode($data)
      ] );

      if ( is_wp_error( $resp ) || wp_remote_retrieve_response_code( $resp ) !== 200 ) {
        return false;
      }

      $body = wp_remote_retrieve_body( $resp );
      $resp_data = json_decode( $body, true );

      if ( array_key_exists('error', $resp_data) ) {
        return false;
      }

      $order_id = $resp_data['success']['orderId'];

      $insert_result = DB::createOrder( $order_id, 'precise', $color, $bg );

      if ( ! $insert_result ) {
        return false;
      }

      return $order_id;

    }

    // Getting order
    static public function getOrder( $order_id, $check_order_exists = false ) {

      if ( $check_order_exists && ! DB::isOrderExists($order_id) ) {
        return false;
      }

      $order_info_url = "https://shapeshift.io/orderInfo/{$order_id}";
      $order_info = Helper::httpRequest( $order_info_url );

      if ( ! $order_info ) {
        return false;
      }

      return $order_info;

    }

    // Getting transactions
    static public function getTransactions( $count ) {

      $transactions_url = "https://shapeshift.io/recenttx/{$count}";
      $transactions_raw = Helper::httpRequest( $transactions_url );
      if ( ! $transactions_raw ) {
        return false;
      }

      $transactions = [];
      foreach ( $transactions_raw as $transaction ) {
        $transactions[] = [
          'from_amount' => $transaction['amount'],
          'from_currency' => $transaction['curIn'],
          'to_currency' => $transaction['curOut'],
          'created' => date( 'Y-m-d H:i:s', $transaction['timestamp'] )
        ];
      }

      return $transactions;

    }

  }
}
