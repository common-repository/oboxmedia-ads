<?php
/**
 * Oboxmedia Ads Plugin
 *
 * @package   oboxmedia-ads-plugin
 * @author    Mathieu Lemelin <mlemelin@oboxmedia.com>
 * @license   GPL-2.0+
 * @link      http://oboxmedia.com
 * @copyright 4-3-2015 Oboxmedia
 */

/**
 * Oboxmedia Ads Plugin class.
 *
 * @package oboxmedia-ads-plugin
 * @author  Mathieu Lemelin <mlemelin@oboxmedia.com>
 */
class OboxmediaAdsPlugin{
    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   1.0.0
     *
     * @var     string
     */
    protected $version = "1.3.1";

    /**
     * Unique identifier for your plugin.
     *
     * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
     * match the Text Domain file header in the main plugin file.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $plugin_slug = "oboxmedia-ads-plugin";

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * @var array
     */
    public $options = array();

    /**
     * Slug of the plugin screen.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $plugin_screen_hook_suffix = null;

    /**
     * Initialize the plugin by setting localization, filters, and administration functions.
     *
     * @since     1.0.0
     */
    private function __construct() {
      
      $options = get_option( 'oboxads_settings' );

      $defaultOptions = array(
        'taxonomy_enabled' => false,
        'ads_txt_enabled' => false,
        'gdpr_enabled' => false,
        'experiment_enabled' => false,
        'oboxads_domain' => '',
        'oboxads_ads_in_posts' => false,
        'tagVersion' => 'v4',
        'manual_init' => false,
        'prefetch_preconnect_enabled' => true,
        'cb_auto_enabled' => false,
        'cb_version_timestamp' => time(),
        'noscript_detection_enabled' => false
      );
      
      if (is_array($options)) {
        $this->options = array_merge($defaultOptions, $options);
      } else {
        $this->options = $defaultOptions;
      } //if

      if (strlen($this->options['oboxads_domain']) <= 0) {
        $domainParts = explode('.', $_SERVER['SERVER_NAME']);
        $this->options['oboxads_domain'] = implode('.', array_slice($domainParts, -2, 1)).'_site';
      } //if

      // Load plugin text domain
      add_action("init", array($this, "load_plugin_textdomain"));

      $this->setup_hooks();

    } // __construct()

    /**
     * setup hooks
     *
     * @since  2017-09-25

     * @author Patrick Forget <patforg@geekpad.ca>
     */
    public function setup_hooks() {
      // Add the options page and menu item.
      add_action("admin_menu", array($this, "add_plugin_admin_menu"));
      add_action("admin_init", array($this,"admin_options_init") );

      // actions, filters and shortcodes
      add_action("wp_head", "oboxads_head_script");

      add_action( 'oboxads_show_ad', 'oboxadsShowAd', 10, 2 );

      add_shortcode( 'oboxads', 'oboxads_shortcode' );
      if (isset($this->options['oboxads_ads_in_posts']) 
        && $this->options['oboxads_ads_in_posts'] === '1') {
        add_filter( 'the_content', 'oboxads_insert_ads_in_posts', 99 );
      } //if

      if ($this->options['ads_txt_enabled']) {
        add_action('parse_request', 'oboxads_show_ads_txt');
        add_action('parse_request', 'oboxads_ads_txt_sync');
      } //if

      if ($this->options['gdpr_enabled']) {
        add_action('parse_request', 'oboxads_show_pubvendors');
      } //if


      if ($this->options['experiment_enabled']) {
        add_action('init', 'oboxads_experiment');
      } //if

      if ($this->options['noscript_detection_enabled']) {
        add_action('wp_footer', 'oboxads_noscript_footer');
      } // if

      add_action("init", "oboxads_taxonomy_register");
      add_action("parse_query", "oboxads_handle_urls");
    } // setup_hooks()
    

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn"t been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Fired when the plugin is activated.
     *
     * @since    1.0.0
     *
     * @param    boolean $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
     */
    public static function activate($network_wide) {
        // TODO: Define activation functionality here
    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @since    1.0.0
     *
     * @param    boolean $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
     */
    public static function deactivate($network_wide) {
        // TODO: Define deactivation functionality here
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        $domain = 'oboxads';
        $locale = apply_filters("plugin_locale", get_locale(), $domain);

        load_textdomain($domain, WP_LANG_DIR . "/" . $domain . "/" . $domain . "-" . $locale . ".mo");
        load_plugin_textdomain($domain, false, dirname(plugin_basename(__FILE__)) . "/lang/");
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        add_options_page( 'Oboxads Settings', 'Oboxads Settings', 'manage_options', 'oboxads', 'oboxads_options_page' );

    }

    /**
     * Register options fields for admin page
     * @since  2015-07-27
     * @author Patrick Forget <patforg@geekpad.ca>
     */
    public function admin_options_init() {
         
        register_setting( 'pluginPage', 'oboxads_settings' );

        add_settings_section(
            'oboxads_pluginPage_section', 
            __( 'Settings for Oboxmedia ads', 'oboxads' ), 
            'oboxads_settings_section_callback', 
            'pluginPage'
        );

        add_settings_field( 
            'oboxads_ads_in_posts', 
            __( 'Insert ads in posts?', 'oboxads' ), 
            'oboxads_ads_in_posts_render', 
            'pluginPage', 
            'oboxads_pluginPage_section' 
        );

        add_settings_field( 
            'oboxads_domain', 
            __( 'Website domain', 'oboxads' ), 
            'oboxads_domain_render', 
            'pluginPage', 
            'oboxads_pluginPage_section' 
        );
    } // admin_options_init()


}
