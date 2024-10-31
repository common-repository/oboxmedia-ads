<?php
/**
 * adds no script tracking in footer
 *
 * @since  2018-01-29

 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxads_noscript_footer() {
  $plugin = OboxmediaAdsPlugin::get_instance();
  $domain = rawurlencode($plugin->options['oboxads_domain']);
  echo <<<HTML
    <noscript><img src="https://events.bigpipes.co/tagman_noscript/pixel?domain=$domain" style="height: 0; width: 0; display: none;" alt="" /></noscript>
HTML;
} // oboxads_noscript_footer()

