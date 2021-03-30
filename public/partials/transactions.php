<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }
?>
<div class="transactions__ce" data-bg="%%bg%%" data-color="%%fg%%" data-count="%%count%%" data-type="%%type%%">
  <div class="header__ce"><?php _e( 'Recent Transactions', 'instant-cryptocurrency-exchange' ); ?></div>
  <?php if ( $transactions ): ?>
  <div class="transactions-list__ce">
    <?php
      foreach ( $transactions as $transaction ):
        $from_amount = InstantCryptoExchange\Helper::formatNumber( $transaction['from_amount'] );
    ?>
    <div class="transaction__ce">
      <div class="info__ce">
        <img src="<?php echo CE_URL . 'public/img/coins/' . InstantCryptoExchange\Helper::getIconName( $transaction['from_currency'] ); ?>">
        <span class="arrow__ce"><i class="fa fa-arrow-right"></i></span>
        <img src="<?php echo CE_URL . 'public/img/coins/' . InstantCryptoExchange\Helper::getIconName( $transaction['to_currency'] ); ?>">
        <span class="text__ce"><?php echo $from_amount; ?></span>
        <span class="currency__ce"><?php echo $transaction['from_currency']; ?></span>
        <span class="text__ce"><?php _e( 'to', 'instant-cryptocurrency-exchange' ); ?></span>
        <span class="currency__ce"><?php echo $transaction['to_currency']; ?></span>
      </div>
      <div class="time__ce" data-time="<?php echo $transaction['created']; ?>"></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
