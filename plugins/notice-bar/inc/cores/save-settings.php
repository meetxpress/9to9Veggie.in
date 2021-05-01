<?php

//$this->print_array($_POST);
/**
 * [nb_settings] => Array
  (
  [layout_1] => Array
  (
  [middle] => Array
  (
  [notice_type] => plain-text
  [notice_text] =>
  [slider] => Array
  (
  [slides] => Array
  (
  [0] =>
  )

  [slide_duration] =>
  )

  [ticker] => Array
  (
  [ticker_label] =>
  [ticker_items] => Array
  (
  [0] =>
  )

  [ticker_direction] => ltr
  [ticker_speed] =>
  [ticker_pause] =>
  )

  [social_icons] => Array
  (
  [label] =>
  [icons] => Array
  (
  [facebook] => Array
  (
  [url] =>
  )

  [twitter] => Array
  (
  [url] =>
  )

  [google-plus] => Array
  (
  [url] =>
  )

  [instagram] => Array
  (
  [url] =>
  )

  [linkedin] => Array
  (
  [url] =>
  )

  )

  )

  )

  )

  [display] => Array
  (
  [display_position] => top-absolute
  [background_color] =>
  [font_color] =>
  [font_size] =>
  [social_icon_background] =>
  [social_icon_color] =>
  )

  )
 */
$sanitize_rule = array( 'notice_text' => 'html' );
$nb_settings = $this->sanitize_array( stripslashes_deep( $_POST['nb_settings'] ), $sanitize_rule );
//$this->print_array($nb_settings);die();
update_option( 'nb_new_settings', $nb_settings );
$redirect_url = admin_url( 'admin.php?page=notice-bar&success=true&msg=1' );
wp_redirect( $redirect_url );
exit;




