<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }
?>
<div id="bookmark-popup__ce" class="bookmark-popup__ce mfp-hide">
  <div class="header__ce"><?php _e( 'Bookmark This Page', 'instant-cryptocurrency-exchange' ); ?></div>
  <div class="content__ce">
    <div class="info__ce"><?php _e( 'To bookmark this page press CMD+D, or you can copy the link from below. You can always come back to this link later to check your status.', 'instant-cryptocurrency-exchange' ); ?></div>
    <div class="link-block__ce">
      <input type="text" class="link__ce" value="<?php echo $current_url; ?>" readonly>
      <div class="copy-link__ce" title="<?php _e( 'Copied!', 'instant-cryptocurrency-exchange' ); ?>" data-clipboard-text="<?php echo $current_url; ?>"><i class="fa fa-copy"></i></div>
    </div>
  </div>
</div>