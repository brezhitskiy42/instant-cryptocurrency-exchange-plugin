<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }
?>
<div class="step2-block__ce" data-method="%%method%%" data-bg="%%bg%%" data-color="%%fg%%">
  <form id="step2__ce">
    <?php if ( 'quick' == $method ): ?>
    <div class="rate__ce"><div class="back__ce"><i class="fa fa-chevron-left"></i></div><?php _e( 'Instant Rate', 'instant-cryptocurrency-exchange' ); ?>: <span>1 %%from_symbol%% = <span class="rate-amount__ce">%%rate%%</span> %%to_symbol%%</span></div>
    <?php endif; ?>
    <?php if ( 'precise' == $method ): ?>
    <div class="rate__ce"><div class="back__ce"><i class="fa fa-chevron-left"></i></div><?php _e( 'Your Rate', 'instant-cryptocurrency-exchange' ); ?>: <span>1 %%from_symbol%% = <span class="rate-amount__ce">%%rate%%</span> %%to_symbol%%</span></div>
    <?php endif; ?>
    <div class="min-max-fee__ce">
      <div class="item__ce min__ce">
        <div class="title__ce"><?php _e( 'Deposit Min', 'instant-cryptocurrency-exchange' ); ?></div>
        <div class="count__ce"><span>%%minimum%%</span> %%from_symbol%%</div>
      </div>
      <div class="item__ce max__ce">
        <div class="title__ce"><?php _e( 'Deposit Max', 'instant-cryptocurrency-exchange' ); ?></div>
        <div class="count__ce"><span>%%limit%%</span> %%from_symbol%%</div>
      </div>
      <?php if ( 'precise' == $method ): ?>
      <div class="item__ce give__ce">
        <div class="title__ce"><?php _e( 'Deposit This', 'instant-cryptocurrency-exchange' ); ?></div>
        <div class="count__ce"><span></span> %%from_symbol%%</div>
      </div>
      <div class="item__ce get__ce">
        <div class="title__ce"><?php _e( 'To Get This', 'instant-cryptocurrency-exchange' ); ?></div>
        <div class="count__ce"><span></span> %%to_symbol%%</div>
      </div>
      <?php endif; ?>
      <div class="item__ce fee_ce">
        <div class="title__ce"><?php _e( 'Miner Fee', 'instant-cryptocurrency-exchange' ); ?></div>
        <div class="count__ce"><span>%%fee%%</span> %%to_symbol%%</div>
      </div>
    </div>
    <div class="deposit-receive__ce<?php if ( 'precise' == $method ) echo ' precise__ce'; ?>">
      <div class="deposit__ce">
        <div class="coin__ce" data-type="from" data-symbol="%%from_symbol%%">
          <img src="%%from_icon%%">
          <div class="name__ce">%%from_name%%</div>
        </div>
      </div>
      <div class="swap__ce"><i class="fa fa-exchange"></i></div>
      <div class="receive__ce">
        <div class="coin__ce" data-type="to" data-symbol="%%to_symbol%%">
          <img src="%%to_icon%%">
          <div class="name__ce">%%to_name%%</div>
        </div>
      </div>
    </div>
    <?php if ( 'precise' == $method ): ?>
    <div class="amount__ce">
      <input type="text" name="deposit_amount" placeholder="<?php _e( 'Deposit Amount', 'instant-cryptocurrency-exchange' ); ?>" data-min="%%minimum%%" data-max="%%limit%%" data-rate="%%rate%%" data-fee="%%fee%%">
      <input type="text" name="receive_amount" placeholder="<?php _e( 'Receive Amount', 'instant-cryptocurrency-exchange' ); ?>" data-min="%%minimum%%" data-max="%%limit%%" data-rate="%%rate%%" data-fee="%%fee%%">
    </div>
    <?php endif; ?>
    <div class="addresses__ce">
      <input type="text" name="destination_address" placeholder="<?php printf( __( 'Your %s Address (destination address)', 'instant-cryptocurrency-exchange' ), $to_name ); ?>">
      <input type="text" name="refund_address" placeholder="<?php printf( __( 'Your %s Refund Address', 'instant-cryptocurrency-exchange' ), $from_name ); ?>">
      <input id="terms__ce" type="checkbox" required="required">
      <label for="terms__ce"><?php printf(__( 'I agree to the <a href="%s" target="_blank">Terms and Conditions</a>.', 'instant-cryptocurrency-exchange' ), '%%terms_page_url%%');?></label>
      <?php if ( 0 == $rate ): ?>
      <div class="error-info__ce rate__ce"><?php _e( 'Market data is not available, please try again later or choose other coins.', 'instant-cryptocurrency-exchange' ); ?></div>
      <?php endif; ?>
      <div class="error-info__ce min-limit__ce"><?php _e( 'Amount is below the minimum limit.', 'instant-cryptocurrency-exchange' ); ?></div>
      <div class="error-info__ce max-limit__ce"><?php _e( 'Amount is above the maximum limit.', 'instant-cryptocurrency-exchange' ); ?></div>
      <div class="error-info__ce all-required__ce"><?php _e( 'All fields are required.', 'instant-cryptocurrency-exchange' ); ?></div>
      <div class="error-info__ce resp__ce"><?php _e( 'There was an error while processing your data.', 'instant-cryptocurrency-exchange' ); ?></div>
      <button type="submit" class="next-btn__ce"<?php if ( 0 == $rate ) echo ' disabled'; ?>><?php _e( 'Start Transaction', 'instant-cryptocurrency-exchange' ); ?></button>
    </div>
  </form>
  <div class="loader__ce">
    <div class="spinner__ce">
      <div class="double-bounce1__ce"></div>
      <div class="double-bounce2__ce"></div>
    </div>
  </div>
</div>
