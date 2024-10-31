<?php
/**
 * Display currently set options
 *
 * @since  2017-09-25

 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxads_show_options() {
  $plugin = OboxmediaAdsPlugin::get_instance();
  echo '<pre>';
  echo var_export($plugin->options, true);
  echo '</pre>';
  exit();
} // oboxads_show_options()

