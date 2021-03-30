<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }
?>
<div class="wrap">
  <h2><?php echo get_admin_page_title(); ?></h2>
  <?php settings_errors(); ?>
  <form action="options.php" method="post">
    <?php
      settings_fields( 'ce_option_group' );
      do_settings_sections( 'ce-settings' );
    ?>
    <button type="button" class="button button-secondary add-new-coin-alert__ce"><?php _e( 'Add new', 'instant-cryptocurrency-exchange' ); ?></button>
    <?php submit_button(); ?>
  </form>
</div>