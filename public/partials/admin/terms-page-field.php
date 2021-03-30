<?php
  // If this file is called directly, abort
  if ( ! defined( 'ABSPATH' ) ) {
    die;
  }

  $ce_option = get_option( 'ce_option' );
  $page_id = $ce_option['terms_page_id'];

  wp_dropdown_pages([
    'depth'                 => 0,
    'child_of'              => 0,
    'selected'              => $page_id ? $page_id : 0,
    'echo'                  => 1,
    'name'                  => 'ce_option[terms_page_id]',
    'id'                    => null, // string
    'class'                 => null, // string
    'show_option_none'      => esc_html__('-- Select page --', 'instant-cryptocurrency-exchange'),
    'show_option_no_change' => '',
    'option_none_value'     => NULL,
  ]);
?>