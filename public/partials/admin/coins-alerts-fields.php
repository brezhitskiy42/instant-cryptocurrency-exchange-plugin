<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }
  
  $coins_list = InstantCryptoExchange\API::getCoinsList();
  if ( ! $coins_list ) {
    $coins_list = [];
  }

  $coins_available = [];
  foreach ( $coins_list as $coin ) {
    if ( 'available' === $coin['status'] ) {
      $coins_available[] = $coin;
    }
  }

  $ce_coins_alerts = get_option( 'ce_coins_alerts' );
?>
<?php if ( $ce_coins_alerts ): foreach ( $ce_coins_alerts as $coin_symbol => $alert ): ?>
<div class="coin-alert__ce">
  <span class="remove-coin-alert__ce"><i class="fa fa-minus-circle"></i></span>
  <select class="coin__ce">
    <?php if ( $coins_available ): foreach( $coins_available as $coin ): ?>
    <option value="<?php echo $coin['symbol']; ?>"<?php if ( $coin_symbol == $coin['symbol'] ) echo ' selected'; ?>><?php echo $coin['name']; ?> [<?php echo $coin['symbol']; ?>]</option>
    <?php endforeach; endif; ?>
  </select>
  <textarea class="regular-text alert__ce"><?php echo $alert; ?></textarea>
  <input type="hidden" name="ce_coins_alerts[<?php echo $coin_symbol; ?>]" value="<?php echo $alert; ?>">
</div>
<?php endforeach; endif; ?>
<div class="coin-alert__ce sample-coin-alert__ce">
  <span class="remove-coin-alert__ce"><i class="fa fa-minus-circle"></i></span>
  <select class="coin__ce">
    <?php if ( $coins_available ): foreach( $coins_available as $coin ): ?>
    <option value="<?php echo $coin['symbol']; ?>"><?php echo $coin['name']; ?> [<?php echo $coin['symbol']; ?>]</option>
    <?php endforeach; endif; ?>
  </select>
  <textarea class="regular-text alert__ce"></textarea>
  <input type="hidden">
</div>