<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }
?>
<div class="step4-block__ce order-status-block__ce" data-status="<?php echo $status; ?>" data-order-id="<?php echo $order_id; ?>" data-bg=<?php echo $bg; ?> data-color=<?php echo $color; ?>>
  <?php if ( 1 == $order['type'] ): ?>
  <div class="rate__ce current-rate__ce" data-from-symbol="<?php echo $from_symbol; ?>" data-to-symbol="<?php echo $to_symbol; ?>"><?php _e( 'Current Exchange Rate', 'instant-cryptocurrency-exchange' ); ?>: <span>1 <?php echo $from_symbol; ?> = <span class="rate-amount__ce"><?php echo $pair_info['rate']; ?></span> <?php echo $to_symbol; ?></span></div>
  <?php endif; ?>
  <?php if ( 2 == $order['type'] ): ?>
  <div class="rate__ce"><?php _e( 'Final Exchange Rate:', 'instant-cryptocurrency-exchange' ); ?> <span>1 <?php echo $from_symbol; ?> = <span class="rate-amount__ce"><?php echo $rate; ?></span> <?php echo $to_symbol; ?></span></div>
  <?php endif; ?>
  <div class="id-bookmark__ce">
    <div class="id__ce"><?php printf( __('Order ID: %s', 'instant-cryptocurrency-exchange'), $order_id ); ?></div>
    <button type="button" class="bookmark__ce"><?php _e( 'Bookmark', 'instant-cryptocurrency-exchange' ); ?></button>
  </div>
  <div class="content__ce">
    <div class="main-text__ce"><?php printf( __('<span class="bold__ce">%s</span> %s successfully received!', 'instant-cryptocurrency-exchange'), $amount, $from_symbol ); ?></div>
    <div class="subtext__ce"><?php _e( 'Please wait while we are exchanging it for you', 'instant-cryptocurrency-exchange' ); ?></div>
  </div>
  <div class="state-block__ce">
    <div class="item__ce done__ce">
      <div class="icon__ce"><i class="fa fa-download"></i></div>
      <div class="title__ce"><?php _e( 'Pending Confirmations', 'instant-cryptocurrency-exchange' ); ?></div>
    </div>
    <div class="item__ce active__ce">
      <div class="icon__ce"><i class="fa fa-exchange"></i></div>
      <div class="title__ce"><?php _e( 'Awaiting Exchange', 'instant-cryptocurrency-exchange' ); ?></div>
    </div>
    <div class="item__ce">
      <div class="icon__ce"><i class="fa fa-check"></i></div>
    </div>
    <div class="progress__ce">
      <div class="step1__ce"></div>
    </div>
  </div>
</div>
