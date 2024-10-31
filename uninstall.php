<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   oboxmedia-wordpress-plugin
 * @author    Mathieu Lemelin <mlemelin@oboxmedia.com>
 * @license   GPL-2.0+
 * @link      http://oboxmedia.com
 * @copyright 4-3-2015 Oboxmedia
 */

// If uninstall, not called from WordPress, then exit
if (!defined("WP_UNINSTALL_PLUGIN")) {
	exit;
}

// TODO: Define uninstall functionality here