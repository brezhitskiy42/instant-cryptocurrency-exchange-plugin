<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }
?>
<div class="step5-block__ce order-status-block__ce" data-status="<?php echo $status; ?>" data-order-id="<?php echo $order_id; ?>" data-bg=<?php echo $bg; ?> data-color=<?php echo $color; ?>>
  <div class="rate__ce"><?php _e( 'Final Exchange Rate:', 'instant-cryptocurrency-exchange' ); ?> <span>1 <?php echo $from_symbol; ?> = <span class="rate-amount__ce"><?php echo $order['rate']; ?></span> <?php echo $to_symbol; ?></span></div>
  <div class="id-bookmark__ce">
    <div class="id__ce"><?php printf( __('Order ID: %s', 'instant-cryptocurrency-exchange'), $order_id ); ?></div>
    <button type="button" class="bookmark__ce"><?php _e( 'Bookmark', 'instant-cryptocurrency-exchange' ); ?></button>
  </div>
  <div class="content__ce">
    <div class="main-text__ce"><?php printf( __('<span class="bold__ce">%s</span> %s was sent!', 'instant-cryptocurrency-exchange'), $amount, $to_name ); ?></div>
    <a href="<?php echo $blockchain_link; ?>" class="blockchain-link__ce" target="_blank"><?php _e( 'See it on the blockchain', 'instant-cryptocurrency-exchange' ); ?></a>
  </div>
  <div class="state-block__ce">
    <div class="item__ce done__ce">
      <div class="icon__ce"><i class="fa fa-download"></i></div>
      <div class="title__ce"><?php _e( 'Pending Confirmations', 'instant-cryptocurrency-exchange' ); ?></div>
    </div>
    <div class="item__ce done__ce">
      <div class="icon__ce"><i class="fa fa-exchange"></i></div>
      <div class="title__ce"><?php _e( 'Exchange Complete', 'instant-cryptocurrency-exchange' ); ?></div>
    </div>
    <div class="item__ce done__ce">
      <div class="icon__ce"><i class="fa fa-check"></i></div>
      <div class="title__ce"><?php _e( 'All Done!', 'instant-cryptocurrency-exchange' ); ?></div>
    </div>
    <div class="progress__ce">
      <div class="step1__ce"></div>
      <div class="step2__ce"></div>
    </div>
  </div>
</div>
