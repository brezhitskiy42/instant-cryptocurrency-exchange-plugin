<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }

  $ce_option = get_option( 'ce_option' );
  $scripts_placing = $ce_option['scripts_placing'];
?>
<select name="ce_option[scripts_placing]">
  <option value="header"<?php if ( 'header' == $scripts_placing ) echo ' selected'; ?>><?php _e( 'Header', 'instant-cryptocurrency-exchange' ); ?></option>
  <option value="footer"<?php if ( 'footer' == $scripts_placing ) echo ' selected'; ?>><?php _e( 'Footer', 'instant-cryptocurrency-exchange' ); ?></option>
</select>