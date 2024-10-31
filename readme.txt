=== Oboxmedia Ads ===
Contributors: patforg, mlemelin, cdpierre
Tags: Oboxmedia.com, advertising, oboxmedia, ads, publishing 
Requires at least: 5.5
Tested up to: 6.6.2
Stable tag: 1.9.8
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add oboxmedia.com ads to your site.

== Description ==

Simplifies implementation of various ad solutions from [Oboxmedia.com](http://oboxmedia.com).  Note that you need to be an approved partner to use this plugin.

To become a partner please contact: bizdev at oboxmedia dot com.

== Installation ==

1. Upload `oboxads/` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use the Oboxmedia Ads widget for sidebars
1. Place `<?php do_action('oboxads_show_ad', 'SECTION'); ?>` in your templates to insert a tag

Available sections are:

* oop: normally placed immediately after the `<body>` tag, used for out of page ads.
* header: for banners on the top of the page outside the main content;
* side: for banners on the sides of the page outside the main content;
* content: for banners within the main content;
* footer: for banners at the bottom of the page outside the main content;



