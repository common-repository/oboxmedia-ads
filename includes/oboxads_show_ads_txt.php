<?php
/**
 * show ads.txt file
 *
 * @since  2017-11-21

 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxads_show_ads_txt() {
  $current_url = rtrim($_SERVER["REQUEST_URI"], '/');
  list($current_url,) = explode('?', $current_url);
  if($current_url === '/ads.txt') {
    header_remove('Content-Type');
    header('Content-Type: text/plain; charset=utf-8');
    echo "# oboxads generated file\n";
    echo get_option('oboxads_ads_txt');
    exit();
  } //if
} // oboxads_show_ads_txt()

