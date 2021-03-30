<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }
?>
<div class="step1-block__ce" data-bg="%%bg%%" data-color="%%fg%%">
  <form id="step1__ce">
    <div class="header__ce"><?php _e( 'Choose coins to exchange', 'instant-cryptocurrency-exchange' ); ?></div>
    <div class="deposit-receive__ce">
      <div class="deposit__ce">
        <div class="title__ce"><?php _e( 'Deposit', 'instant-cryptocurrency-exchange' ); ?></div>
        <div class="coin__ce" data-type="from" data-symbol="%%from_symbol%%">
          <img src="%%from_icon%%">
          <div class="name__ce">%%from_name%%</div>
        </div>
        <button type="button" class="quick-btn__ce<?php if ( 'quick' == $method ) echo ' active__ce'; ?>" data-method="quick"><?php _e( 'Quick', 'instant-cryptocurrency-exchange' ); ?></button>
      </div>
      <div class="swap__ce"><i class="fa fa-exchange"></i></div>
      <div class="receive__ce">
        <div class="title__ce"><?php _e( 'Receive', 'instant-cryptocurrency-exchange' ); ?></div>
        <div class="coin__ce" data-type="to" data-symbol="%%to_symbol%%">
          <img src="%%to_icon%%">
          <div class="name__ce">%%to_name%%</div>
        </div>
        <button type="button" class="precise-btn__ce<?php if ( 'precise' == $method ) echo ' active__ce'; ?>" data-method="precise"><?php _e( 'Precise', 'instant-cryptocurrency-exchange' ); ?></button>
      </div>
    </div>
    <input type="hidden" name="method_ce" value="%%method%%">
    <button type="submit" class="next-btn__ce"><?php _e( 'Continue', 'instant-cryptocurrency-exchange' ); ?></button>
  </form>
  <div class="loader__ce">
    <div class="spinner__ce">
      <div class="double-bounce1__ce"></div>
      <div class="double-bounce2__ce"></div>
    </div>
  </div>
</div>
