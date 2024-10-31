<?php
/**
 * adds adblock tracking in footer
 *
 * @since  2018-01-29

 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxads_tracking_init() {
  if (isset($_COOKIE['bpuid'])) {
    $uid = $_COOKIE['bpuid'];
  } else {
    $uid = uniqid(rand() ."_", true);
  } // if

  if (isset($_GET['bpclickid'])) {
    $sid = $_GET['bpclickid'];
  } elseif (!isset($_COOKIE['bpsid'])) {
    $sid = uniqid(rand() ."_", true);
  } else {
    $sid = $_COOKIE['bpsid'];
  } // if

  $_COOKIE['bpuid'] = $uid;
  setcookie('bpuid', $uid, strtotime('+1 year'));

  $_COOKIE['bpsid'] = $sid;
  setcookie('bpsid', $sid, strtotime('+20 minutes'));
} // oboxads_tracking_init()

