<?php
/**
 * show pubvendors json file
 *
 * @since  2017-11-21

 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxads_show_pubvendors() {
  $current_url = rtrim($_SERVER["REQUEST_URI"], '/');
  list($current_url,) = explode('?', $current_url);

  switch ($current_url) {
    case '/.well-known/vendors.json':
    case '/pubvendors.json':
      header('Content-Type: application/json; charset=utf-8');
      echo get_option('oboxads_pubvendors_json');
      exit();
      break;
  } // switch
} // oboxads_show_ads_txt()

