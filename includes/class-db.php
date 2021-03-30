<?php

namespace InstantCryptoExchange;

// If this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) {
  die;
}

// Class for handling database operations
if ( ! class_exists('DB') ) {
  class DB {

    // Creating order
    static public function createOrder( $order_id, $type, $color, $bg ) {

      $order = API::getOrder( $order_id );

      if ( ! $order ) {
        return false;
      }

      global $wpdb;

      $table_name = $wpdb->prefix . 'ce_orders';

      $data = [
        'order_id' => $order_id,
        'type' => $type,
        'from_amount' => $order['incomingCoin'],
        'from_currency' => $order['incomingType'],
        'to_amount' => $order['outgoingCoin'],
        'to_currency' => $order['outgoingType'],
        'rate' => $order['rate'],
        'status' => $order['status'],
        'created' => current_time( 'mysql' ),
        'color' => $color,
        'bg' => $bg
      ];

      $insert_result = $wpdb->insert( $table_name, $data );

      return $insert_result;

    }

    // Updating order
    static public function updateOrder( $order, $order_id ) {

      global $wpdb;

      $table_name = $wpdb->prefix . 'ce_orders';

      $from_amount = isset($order['incomingCoin']) ? $order['incomingCoin'] : null;
      $to_amount = isset($order['outgoingCoin']) ? $order['outgoingCoin'] : null;
      $status = $order['status'];

      $data = [ 'status' => $status ];
      if ( $from_amount ) {
        $data['from_amount'] = $from_amount;
      }
      if ( $to_amount ) {
        $data['to_amount'] = $to_amount;
      }
      $data['rate'] = $order['rate'];

      $where = [ 'order_id' => $order_id ];

      $wpdb->update( $table_name, $data, $where );

    }

    // Getting all orders
    static public function getOrders() {

      global $wpdb;

      $table_name = $wpdb->prefix . 'ce_orders';

      $orders = $wpdb->get_results(
        "SELECT id, order_id, type, from_amount, from_currency, to_amount, to_currency, status, created FROM {$table_name}", ARRAY_A
      );

      return $orders;

    }

    // Getting bg and color from order
    static public function getColors( $order_id ) {

      global $wpdb;

      $table_name = $wpdb->prefix . 'ce_orders';

      $order = $wpdb->get_row( $wpdb->prepare("SELECT color, bg FROM {$table_name} WHERE order_id = %s", $order_id), ARRAY_A );

      return $order;

    }

    // Checking if order exists in db
    static public function isOrderExists( $order_id ) {

      global $wpdb;

      $table_name = $wpdb->prefix . 'ce_orders';

      $order = $wpdb->get_var( $wpdb->prepare("SELECT id FROM {$table_name} WHERE order_id = %s", $order_id) );

      if ( ! $order ) {
        return false;
      }

      return true;

    }

    // Getting rate
    static public function getRate( $order_id ) {

      global $wpdb;

      $table_name = $wpdb->prefix . 'ce_orders';

      $rate = $wpdb->get_var( $wpdb->prepare("SELECT rate FROM {$table_name} WHERE order_id = %s", $order_id) );

      return $rate;

    }

    // Getting transactions
    static public function getTransactions( $count ) {

      global $wpdb;

      $table_name = $wpdb->prefix . 'ce_orders';

      $transactions = $wpdb->get_results(
        $wpdb->prepare("SELECT from_amount, from_currency, to_currency, created FROM {$table_name} WHERE status = 'complete' ORDER BY created DESC LIMIT %d", $count),
        ARRAY_A
      );

      foreach ( $transactions as &$transaction ) {
        $transaction['created'] = get_gmt_from_date( $transaction['created'] );
      }

      return $transactions;

    }

  }
}
