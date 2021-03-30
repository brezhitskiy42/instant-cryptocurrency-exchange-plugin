<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }
?>

<div class="wrap">
  <h2><?php echo get_admin_page_title(); ?></h2>
      
  <table id="transactions-list__ce">
    <thead>
      <tr>
        <th><?php _e( 'Order ID', 'instant-cryptocurrency-exchange' ); ?></th>
        <th><?php _e( 'Type', 'instant-cryptocurrency-exchange' ); ?></th>
        <th><?php _e( 'From', 'instant-cryptocurrency-exchange' ); ?></th>
        <th><?php _e( 'To', 'instant-cryptocurrency-exchange' ); ?></th>
        <th><?php _e( 'Status', 'instant-cryptocurrency-exchange' ); ?></th>
        <th><?php _e( 'Created', 'instant-cryptocurrency-exchange' ); ?></th>
        <th><?php _e( 'API response', 'instant-cryptocurrency-exchange' ); ?></th>
        <th><?php _e( 'Order page', 'instant-cryptocurrency-exchange' ); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php
        $ce_option = get_option( 'ce_option' );
        $status_slug = $ce_option['status_slug'];  
      
        if ( $orders ): foreach ( $orders as $order ):
          $order_id = $order['order_id'];
          $type = ucfirst( $order['type'] );
          $from = ( $order['from_amount'] == 0 ? '' : $order['from_amount'] . ' ' ) . $order['from_currency'];
          $to = ( $order['to_amount'] == 0 ? '' : $order['to_amount'] . ' ' ) . $order['to_currency'];
          $status = InstantCryptoExchange\Helper::getStatusText( $order['status'] );
          $link = get_home_url() . "/{$status_slug}/$order_id";
      ?>
      <tr>
        <td><?php echo $order_id; ?></td>
        <td><?php echo $type; ?></td>
        <td><?php echo $from; ?></td>
        <td><?php echo $to; ?></td>
        <td><?php echo $status; ?></td>
        <td data-order="<?php echo $order['id']; ?>"><?php echo $order['created']; ?></td>
        <td><a href="javascript:;" class="view-order__ce" data-order-id="<?php echo $order_id; ?>"><?php _e( 'View', 'instant-cryptocurrency-exchange' ); ?></a></td>
        <td><a href="<?php echo $link; ?>" target="_blank"><?php _e( 'View', 'instant-cryptocurrency-exchange' ); ?></a></td>
      </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>


<div id="view-api-response__ce" class="view-api-response__ce mfp-hide">
  <div class="api-response__ce">
    <pre><code class="language-javascript" id="code__ce"></code></pre>
  </div>
  <div class="loader__ce black-bg__ce">
    <div class="spinner__ce">
      <div class="double-bounce1__ce"></div>
      <div class="double-bounce2__ce"></div>
    </div>
  </div>
</div>