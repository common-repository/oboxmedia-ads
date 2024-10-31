<?php
/**
 * Handles experiementation
 *
 * @since  2018-01-29

 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxads_experiment() {

  $triggerValue = "";

  $expCookieValue = isset($_COOKIE['_gaexp']) ? 
    $_COOKIE['_gaexp'] : "";

  $triggerCookieValue = isset($_COOKIE['oboxads-exp-trigger']) ? 
    $_COOKIE['oboxads-exp-trigger'] : "";

  // if we already set the cookie and no experiemnts
  // were assigned, set trigger to false
  if ($triggerCookieValue === 'true') {
    $triggerValue = 'false';

  //if already assigned an experiement, set flag to running
  } elseif ( $expCookieValue !== "") {
    if ($expCookieValue !== 'running') {
      $triggerValue = 'running';
    } //if
  //if no flag and no experiement, set flag
  } else {
    $triggerValue = 'true';
  } //if
  
  if ($triggerValue !== "" && !headers_sent()) {
    setcookie('oboxads-exp-trigger', $triggerValue); 
  } //if

} // oboxads_experiment()

