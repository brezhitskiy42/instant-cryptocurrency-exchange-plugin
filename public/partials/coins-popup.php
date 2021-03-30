<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }
?>
<div id="coins-popup__ce" class="coins-popup__ce mfp-hide">
  <input type="text" class="search-coins__ce" placeholder="<?php _e( 'Search by coin name or symbol', 'instant-cryptocurrency-exchange' ); ?>">
  <div class="coins-list__ce" data-type="from" data-step="step1">
    <?php if ( $coins_available ): foreach ( $coins_available as $coin_available ): $alert = ( isset($coins_alerts[$coin_available['symbol']]) ) ? $coins_alerts[$coin_available['symbol']] : false; ?>
    <div class="coin__ce" data-name="<?php echo strtolower( $coin_available['name'] ); ?>" data-symbol="<?php echo $coin_available['symbol']; ?>"<?php if ( $alert ) echo ' data-alert="' . $alert . '"'; ?>>
      <img src="<?php echo CE_URL . 'public/img/coins/' . InstantCryptoExchange\Helper::getIconName( $coin_available['symbol'] ); ?>">
      <div class="name__ce"><?php echo $coin_available['name']; ?></div>
    </div>
    <?php endforeach; endif; ?>
    <?php if ( $coins_unavailable ): foreach ( $coins_unavailable as $coin_unavailable ): ?>
    <div class="coin__ce unavailable__ce" data-name="<?php echo strtolower( $coin_unavailable['name'] ); ?>" data-symbol="<?php echo $coin_unavailable['symbol']; ?>">
      <img src="<?php echo CE_URL . 'public/img/coins/' . InstantCryptoExchange\Helper::getIconName( $coin_unavailable['symbol'] ); ?>">
      <div class="name__ce"><?php echo $coin_unavailable['name']; ?></div>
      <div class="unavailable-text__ce"><?php _e( 'Temporarily unavailable', 'instant-cryptocurrency-exchange' ); ?></div>
    </div>
    <?php endforeach; endif; ?>
  </div>
</div>