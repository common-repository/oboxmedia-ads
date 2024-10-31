<?php
/**
 * show ads.txt file
 *
 * @since  2021-11-30

 * @author Carl-Darlens Pierre <cdpierre@obox.group>
 */

function oboxads_ads_txt_sync() {
  $current_url = rtrim($_SERVER["REQUEST_URI"], '/');
  list($current_url,) = explode('?', $current_url);
  if ($current_url === '/bigpipes-ads-txt-sync') {
      $plugin = OboxmediaAdsPlugin::get_instance();
      $key = sanitize_text_field($plugin->options['oboxads_domain']);

      try {
        $ch = curl_init();
        // Check if initialization had gone wrong*    
        if ($ch === false) {
            throw new Exception('Failed to initialize curl');
        }

        curl_setopt($ch, CURLOPT_URL, "https://api.bigpipes.co/v1.5/tag-manager/ads-txt-update/pull/$key");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
        $results = curl_exec($ch);
      
        // Check the return value of curl_exec(), too
        if ($results === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        } else {
          $data = json_decode($results);
          $ads_txt = $data->data;
          update_option('oboxads_ads_txt', $ads_txt, 'no');
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
        exit();
      } catch (\Exception $e) {
        echo json_encode(['status' => 'failed', 'message' => $e->getMessage() ]);
        exit();
      }

  } //if
} // oboxads_show_ads_txt()

