<?php
/**
 * updates options based on some post data
 *
 * @since  2017-09-25

 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxads_update_options($newOptions) {

  update_option('oboxads_settings', $newOptions);

  $plugin = OboxmediaAdsPlugin::get_instance();
  $plugin->options = $newOptions;
} // oboxads_update_options()

