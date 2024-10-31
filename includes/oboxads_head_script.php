<?php

/**
 * returns javascript code needed to lod tag manager
 *
 * @since  2017-09-25

 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxads_head_script() {

  $plugin = OboxmediaAdsPlugin::get_instance();

  $tagVersion = $plugin->options['tagVersion'];
  $tagVersion = apply_filters('oboxads_tag_version', $tagVersion);
  if (isset($_GET['oboxads_tag_version'])) {
    $tagVersion = $_GET['oboxads_tag_version'];
  } //if

  if (defined('APPLICATION_ENV') && APPLICATION_ENV === 'DEV') {
    #$baseURL = "//local.cdn.oboxads.com/$tagVersion";
    $baseURL = "//static.tagman.ca";
  } else {
    #$baseURL = "//cdn.oboxads.com/$tagVersion";
    $baseURL = "//static.tagman.ca";
  } //if

  $manualInit = $plugin->options['manual_init'];
  $manualInit = apply_filters('oboxads_manual_init', $manualInit);

  $postId = get_queried_object_id();
  $domain = (isset($plugin->options['oboxads_domain']) ? $plugin->options['oboxads_domain'] :  '');
  
  $domain = apply_filters('oboxads_domain', $domain);

  if (isset($_GET['oboxads-domain'])) {
    $domain = $_GET['oboxads-domain'];
  } //if

  if ( $plugin->options['experiment_enabled'] ) {

    $expCookieValue = isset($_COOKIE['_gaexp']) ? 
      $_COOKIE['_gaexp'] : "";

    if ($expCookieValue !== "") {
      // remove GAX1.2 from _gaexp cookie
      $expCookieValue = preg_replace('/GAX\d+\.\d+\./', '', $expCookieValue);

      // if multiple experiements are assigned they will be separated by a !
      $experiments = explode('!', $expCookieValue);

      // take the last experiment (should only have one)
      // @todo handle multiple experiments
      $currentExperiment =  array_pop($experiments); 

      $parts = explode('.', $currentExperiment);

      //extract variant
      $variantId = array_pop($parts);
      
      // we dont care about expiration
      $expiration = array_pop($parts);

      // the rest is experimentId
      $expId = implode('.', $parts);

      $domain = "experiments/{$expId}/{$variantId}/{$domain}";
    } //if
  } //if

  if (is_home()) {
    $contentType = 'homepage';
  } elseif (is_category()) {
    $contentType = 'categories';
  } elseif (is_single()) {
    $contentType = 'articles';
  } elseif (is_archive()) {
    $contentType = 'archives';
  } else {
    $contentType = 'other';
  } //if

  $contentCategories = array();
  if ($plugin->options['taxonomy_enabled'] && is_single()) {
    $terms = get_the_terms(get_the_ID(), 'oboxads_content');    
    if (is_array($terms)) {
      foreach($terms as $term) {
        $key = $term->description;
        $contentCategories[$key] = array(
          'key' => $key,
          'name' => $term->name,
        );
      } //foreach
    } // if
  } //if
  $contentCategoriesJson = json_encode($contentCategories);
  $cb = 'Date.now() - (Date.now() % 3.6e+6)';
  
  if (!$plugin->options['cb_auto_enabled'] && array_key_exists('cb_version_timestamp', $plugin->options)) {
    $cb = $plugin->options['cb_version_timestamp'];
  }

  if ($tagVersion === 'v4') {
    echo <<<HTML
      <!-- OBOXADS Begin -->
HTML;
    if ($plugin->options['prefetch_preconnect_enabled']) {
      echo <<<HTML
      <link rel="dns-prefetch" href="https://static.tagman.ca/" />
      <link rel="dns-prefetch" href="https://securepubads.g.doubleclick.net/" />
      <link rel="preconnect" href="https://static.tagman.ca/" />
      <link rel="preconnect" href="https://securepubads.g.doubleclick.net/" />
HTML;
    }
    echo <<<HTML
    
      <script>
      (function (w,d,s,n,g,u) {
          var cs = d.getElementsByTagName(s)[0],
              ns = d.createElement(s),
              cb = {$cb}
          w[n] = w[n] || [];
          w[n].ts = Date.now();
          w[g] = w[g] || {};
          w[g].cmd = w[g].cmd || [];

          ns.async = true;
          ns.src = '{$baseURL}/{$tagVersion}/sites/'+ u +'.js?cb='+ cb;
          cs.parentNode.insertBefore(ns, cs);
      })(window, document, 'script', 'OBOXADSQ', 'googletag', '{$domain}');
      </script>
      <script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
HTML;

  }
  echo <<<HTML
    <script>
        OBOXADSQ.push({
            "postId": "{$postId}",
            "contentType": "{$contentType}",
            "startTime": new Date().getTime(),
            "contentCategories": {$contentCategoriesJson},
            "cmd": "config"
        });
    </script>
    <!-- OBOXADS End -->
HTML;
} // oboxads_head_script()

