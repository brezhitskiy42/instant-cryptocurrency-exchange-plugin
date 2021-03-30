<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }
?>
<div class="step3-block__ce order-status-block__ce" data-status="<?php echo $status; ?>" data-order-id="<?php echo $order_id; ?>" data-bg=<?php echo $bg; ?> data-color=<?php echo $color; ?>>
  <?php if ( 1 == $order['type'] ): ?>
  <div class="rate__ce current-rate__ce" data-from-symbol="<?php echo $from_symbol; ?>" data-to-symbol="<?php echo $to_symbol; ?>"><?php _e( 'Current Exchange Rate', 'instant-cryptocurrency-exchange' ); ?>: <span>1 <?php echo $from_symbol; ?> = <span class="rate-amount__ce"><?php echo $pair_info['rate']; ?></span> <?php echo $to_symbol; ?></span></div>
  <?php endif; ?>
  <?php if ( 2 == $order['type'] ): ?>
  <div class="rate__ce"><?php _e( 'Final Exchange Rate:', 'instant-cryptocurrency-exchange' ); ?> <span>1 <?php echo $from_symbol; ?> = <span class="rate-amount__ce"><?php echo $order['rate']; ?></span> <?php echo $to_symbol; ?></span></div>
  <?php endif; ?>
  <div class="id-bookmark__ce">
    <div class="id__ce"><?php printf( __('Order ID: %s', 'instant-cryptocurrency-exchange'), $order_id ); ?></div>
    <button type="button" class="bookmark__ce"><?php _e( 'Bookmark', 'instant-cryptocurrency-exchange' ); ?></button>
  </div>
  <?php if ( 'no_deposits' == $status ): ?>
  <div class="content__ce">
    <?php if ( 1 == $order['type'] ): ?>
    <div class="main-text__ce no-deposits__ce quick__ce" data-from-symbol="<?php echo $from_symbol; ?>" data-to-symbol="<?php echo $to_symbol; ?>"><?php printf( __('Send any amount<br>from <span class="bold__ce from-amount__ce">%s</span> %s to <span class="bold__ce to-amount__ce">%s</span> %s<br>to the address below', 'instant-cryptocurrency-exchange'), $pair_info['minimum'], $from_symbol, $pair_info['limit'], $from_symbol ); ?></div>
    <?php endif; ?>
    <?php if ( 2 == $order['type'] ): ?>
    <div class="main-text__ce"><?php printf( __('Send <span class="bold__ce">%s</span> %s to the address below', 'instant-cryptocurrency-exchange'), $amount, $from_symbol ); ?></div>
    <div class="time__ce"><?php _e( 'Time Remaining', 'instant-cryptocurrency-exchange' ); ?>: <span id="countdown__ce" data-time="<?php echo $order['timeRemaining']; ?>"></span></div>
    <?php endif; ?>
    <div class="subtext__ce"><?php _e( 'The whole amount should be sent as one transaction', 'instant-cryptocurrency-exchange' ); ?></div>
    <div class="deposit-address__ce"><?php echo $deposit; ?></div>
    <div class="copy-address__ce" title="<?php _e( 'Copied!', 'instant-cryptocurrency-exchange' ); ?>" data-clipboard-text="<?php echo $deposit; ?>"><i class="fa fa-copy"></i></div>
    <canvas class="qr-code__ce" id="qr__ce"></canvas>
  </div>
  <?php endif; ?>
  <?php if ( 'expired' == $status ): ?>
  <div class="content__ce">
    <div class="main-text__ce"><?php _e( 'Time has expired, no deposit received', 'instant-cryptocurrency-exchange' ); ?></div>
  </div>
  <?php endif; ?>
  <?php if ( 'resolved' == $status ): ?>
  <div class="content__ce">
    <div class="main-text__ce"><?php _e( 'There was an error during sending deposit', 'instant-cryptocurrency-exchange' ); ?></div>
    <div class="subtext__ce"><?php _e( 'Your amount will be refund', 'instant-cryptocurrency-exchange' ); ?></div>
  </div>
  <?php endif; ?>
  <div class="state-block__ce">
    <div class="item__ce <?php echo $state_class; ?>">
      <div class="icon__ce"><i class="fa fa-download"></i></div>
      <div class="title__ce"><?php _e( 'Awaiting Deposit', 'instant-cryptocurrency-exchange' ); ?></div>
    </div>
    <div class="item__ce">
      <div class="icon__ce"><i class="fa fa-exchange"></i></div>
      <div class="title__ce"><?php _e( 'Awaiting Exchange', 'instant-cryptocurrency-exchange' ); ?></div>
    </div>
    <div class="item__ce">
      <div class="icon__ce"><i class="fa fa-check"></i></div>
    </div>
    <div class="progress__ce"></div>
  </div>
</div>
