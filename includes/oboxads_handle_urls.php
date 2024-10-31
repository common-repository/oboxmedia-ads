<?php
/**
 * calls custom functions based on GET params
 *
 * @since  2017-09-25

 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxads_handle_urls() {
  static $ran = false;
  if ($ran) {
    return;
  } //if
  $ran = true;
  if (isset($_GET['oboxads_show_options'])) {
    oboxads_show_options();
  } //if

  if (isset($_GET['oboxads_update_options'])) {
    $plugin = OboxmediaAdsPlugin::get_instance();

    if (isset($_GET['oboxads_content_tax'])) {
      $plugin->options['taxonomy_enabled'] = $_GET['oboxads_content_tax'] === '0' ? false : true;
    } //if

    if (isset($_GET['oboxads_ads_txt'])) {
      $plugin->options['ads_txt_enabled'] = $_GET['oboxads_ads_txt'] === '0' ? false : true;
    } //if

    if (isset($_GET['oboxads_gdpr'])) {
      $plugin->options['gdpr_enabled'] = $_GET['oboxads_gdpr'] === '0' ? false : true;
    } //if


    if (isset($_GET['prefetch_preconnect_enabled'])) {
      $plugin->options['prefetch_preconnect_enabled'] = $_GET['prefetch_preconnect_enabled'] === '0' ? false : true;
    } //if

    if (isset($_GET['cb_auto_enabled'])) {
      $plugin->options['cb_auto_enabled'] = $_GET['cb_auto_enabled'] === '0' ? false : true;
      if ($plugin->options['cb_auto_enabled']) {
        $plugin->options['cb_version_timestamp'] = time();
      }
    }

    if (isset($_GET['experiment_enabled'])) {
      $plugin->options['experiment_enabled'] = $_GET['experiment_enabled'] === '0' ? false : true;
    } //if

    if (isset($_GET['noscript_detection_enabled'])) {
      $plugin->options['noscript_detection_enabled'] = $_GET['noscript_detection_enabled'] === '0' ? false : true;
    } //if

    if (isset($_GET['manual_init'])) {
      $plugin->options['manual_init'] = $_GET['manual_init'] === '0' ? false : true;
    } //if

    if (isset($_GET['oboxads_tag_version'])) {
      $tagVersion = $plugin->options['tagVersion']; 
      switch (strtolower($_GET['oboxads_tag_version'])) {
        case 'v3':
        case 'v4':
        case 'hybrid':
          $tagVersion = strtolower($_GET['oboxads_tag_version']);
          break;
      } //switch
      $plugin->options['tagVersion'] = $tagVersion;
    } //if
    oboxads_update_options($plugin->options);
  } //if

  if (isset($_GET['oboxads_ads_clear_cache'])) {
    $timestamp = time();
    $plugin = OboxmediaAdsPlugin::get_instance();
    $plugin->options['cb_version_timestamp'] = $timestamp;
    oboxads_update_options($plugin->options);
    exit("Cache cleared! new version id: $timestamp");
  } //if

  if (isset($_GET['oboxads_ads_txt_sync'])) {

    $plugin = OboxmediaAdsPlugin::get_instance();

    if (defined('APPLICATION_ENV') && APPLICATION_ENV === 'DEV') {
      $baseURL = "https://local.api.oboxads.com/";
    } else {
      $baseURL = "https://api.oboxads.com/";
    } //if

    $domain = sanitize_text_field($plugin->options['oboxads_domain']);
    $ads_txt_url = $baseURL ."rest/v1/site-config/" . urlencode($domain) ."/ads.txt?cb=". microtime(true);  

    try {
      $ads_txt = @file_get_contents($ads_txt_url);
      if ($ads_txt === FALSE) {
        $error = error_get_last();
        echo $ads_txt_url;
        throw new \Exception($error);
      }
    } catch (\Exception $e) {
      if (function_exists('curl_init') === true) {
        // update ads.txt with curl
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $ads_txt_url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($c);
        curl_close($c);
        if ($result !== false) {
          $ads_txt = $result;
        } else {
          exit('Curl error: '. curl_error($c));
        }
      }
    }

    if ($ads_txt === 'not found') {
        exit('File not found: '. $ads_txt_url);
    }

    if (strlen($ads_txt) > 0) {
      update_option('oboxads_ads_txt', $ads_txt, 'true');
      exit("updated from ". $ads_txt_url ." with: \n\n<pre>". $ads_txt);
    } else {
      exit('empty response from '. $ads_txt_url);
    } //if

  } //if

  if (isset($_GET['oboxads_pubvendors_sync'])) {

    $plugin = OboxmediaAdsPlugin::get_instance();
    $pubvendors_url = "https://api.oboxmedia.com/pubvendors.json";
    $pubvendors_text = file_get_contents($pubvendors_url);
    
    if(strlen($pubvendors_text) > 0) {
      update_option('oboxads_pubvendors_json', $pubvendors_text, 'no');
      exit("updated from ". $pubvendors_url ." with: \n\n<pre>". $pubvendors_text);
    } else {
      exit('empty response from '. $pubvendors_url);
    } //if
  } //if

  if (isset($_GET['oboxads_content_tax_sync'])) {
    $plugin = OboxmediaAdsPlugin::get_instance();
    if (defined('APPLICATION_ENV') && APPLICATION_ENV === 'DEV') {
      $baseURL = "https://local.api.oboxads.com/";
    } else {
      $baseURL = "https://api.oboxads.com/";
    } //if
    $domain = sanitize_text_field($plugin->options['oboxads_domain']);
    
    $siteConfigURL = $baseURL ."rest/v1/site-config/" . urlencode($domain);  
    $json = file_get_contents($siteConfigURL);
    $siteConfig = json_decode($json, true);

    oboxads_taxonomy_sync($siteConfig, array('clearAll' => false));
  } //if
} // oboxads_handle_urls()

