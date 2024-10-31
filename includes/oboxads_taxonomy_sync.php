<?php
/**
 * register content taxonomies
 *
 * @since  2017-09-25

 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxads_taxonomy_sync($siteConfig, $options) {

  $plugin = OboxmediaAdsPlugin::get_instance();

  echo "<pre>Starting sync". PHP_EOL;

  $localTerms = get_terms( array( 'taxonomy' => 'oboxads_content', 'hide_empty' => 0 ) );
  if (isset($options['clearAll']) && $options['clearAll'] === true) {
    $termIds = array();
    foreach($localTerms as $term) {
      wp_delete_term($term->term_id, 'oboxads_content');
    } //foreach
    echo "Terms deleted". PHP_EOL;
    $localTerms = array();
  } //if

  // category key is stored in the description
  $localDescriptionMap = array();
  $localTermIdMap = array();
  foreach ($localTerms as $term) {
    $key = $term->description;
    $localDescriptionMap[$key] = $term->term_id;
    $localTermIdMap[$term->term_id] = $term;
  } //for
      
  $localTermsToDelete = $localDescriptionMap;
  $termsToAdd = array(
    'parents' => array(),
    'children' => array() 
  );
  $parentIdKeyMap = array();

  foreach($siteConfig['content_category'] as $categoryToAdd) {
    $key = $categoryToAdd['category_key'];

    $isParent = (strpos($key, '-') === false);

    if ($isParent) {
      $parentIdKeyMap[$categoryToAdd['term_id']] = $key;
    } //if

    // check if already exists
    if (isset($localTermsToDelete[$key])) {
      // remove from delte list if still site config
      unset($localTermsToDelete[$key]);
    } else {
      if ($isParent) {
        $termsToAdd['parents'][] = $categoryToAdd;
      } else {
        $termsToAdd['children'][] = $categoryToAdd;
      } //if
    } //if

  } //foreach

  $addTerm = function ($term) use (&$localDescriptionMap, &$parentIdKeyMap) {

    if (!is_array($term)) {
      return;
    } //if

    $name = isset($term['name']) ? sanitize_text_field($term['name']) : false;

    if ($name === false) {
      echo "name not set". PHP_EOL;
      return;
    } //if

    $options = array();
    
    $key = isset($term['category_key']) ? sanitize_text_field($term['category_key']) : false;
    if ($key === false) {
      echo "Key not set". PHP_EOL;
      return;
    } //if

    $options['description'] = $key;

    $parent = isset($term['parent']) && is_numeric($term['parent']) ? sanitize_text_field($term['parent']) : false;
    if ($parent !== false) {
      // create parent
      if (!isset($parentIdKeyMap[$parent])) {
        echo "Parent ($parent) does not exist for {$name}". PHP_EOL;
        return;
      } //if
      $parentKey = $parentIdKeyMap[$parent]; 
      $options['parent'] = $localDescriptionMap[$parentKey];
    } //if


    $action = '';
    if (isset($localDescriptionMap[$key])) {
      $action = 'Updated';
      $termInfo = wp_update_term($localDescriptionMap[$key], 'oboxads_content', $options);
    } else {
      $action = 'Added';
      $termInfo = wp_insert_term($name, 'oboxads_content', $options);
    } //if

    if (is_array($termInfo)) {
      echo "{$action} {$name}". PHP_EOL;
      $term = get_term($termInfo['term_id'], 'oboxads_content');
      $localDescriptionMap[$term->description] = $term->term_id;
    } else {
      var_export($termInfo);
      var_export($options);
    } //if
  };

  echo "Adding Parents(". count($termsToAdd['parents']) .")". PHP_EOL;
  foreach ($termsToAdd['parents'] as $newTerm) {
    $addTerm($newTerm);
  } //foreach 

  echo "Adding Children(". count($termsToAdd['children']) .")". PHP_EOL;
  foreach ($termsToAdd['children'] as $newTerm) {
    $addTerm($newTerm);
  } //foreach 

  echo "Removing (". count($localTermsToDelete) .")". PHP_EOL;
  foreach ($localTermsToDelete as $key => $termId) {

    if (!isset($localTermIdMap[$termId])) {
      echo "Local term does not exist ($termId)" . PHP_EOL;
      continue;
    } //if

    $term =&$localTermIdMap[$termId];
    if ($term->count > 0) {
      echo "Local term {$term->name} has posts mapped to it ({$term->count})" . PHP_EOL;
      continue;
    } //if

    $termInfo = wp_delete_term($termId, 'oboxads_content');

    echo "Removed term {$term->name} with key $key". PHP_EOL;

  } //foreach
  

  echo "sync done";
  exit();

} // oboxads_taxonomy_sync()

