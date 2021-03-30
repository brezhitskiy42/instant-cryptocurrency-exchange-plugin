<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }

  $ce_option = get_option( 'ce_option' );
  $background = $ce_option['background'];
?>
<select name="ce_option[background]">
  <option value="white"<?php if ( 'white' == $background ) echo ' selected'; ?>><?php _e( 'White', 'instant-cryptocurrency-exchange' ); ?></option>
  <option value="black"<?php if ( 'black' == $background ) echo ' selected'; ?>><?php _e( 'Black', 'instant-cryptocurrency-exchange' ); ?></option>
</select>
