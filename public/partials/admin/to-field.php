<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }
  
  $ce_option = get_option( 'ce_option' );
  $to = $ce_option['to'];
  
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
?>
<select name="ce_option[to]">
  <?php if ( $coins_available ): foreach( $coins_available as $coin ): ?>
  <option value="<?php echo $coin['symbol']; ?>"<?php if ( $to == $coin['symbol'] ) echo ' selected'; ?>><?php echo $coin['name']; ?> [<?php echo $coin['symbol']; ?>]</option>
  <?php endforeach; endif; ?>
</select>