<?php
/**
 * register content taxonomies
 *
 * @since  2017-09-25

 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxads_taxonomy_register() {

  $plugin = OboxmediaAdsPlugin::get_instance();

  $taxEnabled = isset($plugin->options['taxonomy_enabled']) && $plugin->options['taxonomy_enabled'] ? true : false;

  if ($taxEnabled) {

    // create a new taxonomy
    register_taxonomy(
      'oboxads_content',
      array( 'post', 'page'),
      array(
        'label' => __( 'Oboxmedia Content Category' ),
        'public' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => false,
        'show_tagcloud' => false,
        'show_ui' => $taxEnabled,
        'hierarchical' => true,
        /* // uncomment to hide in admin
        'capabilities' => array(
          'manage_terms' => '',
          'edit_terms' => '',
          'delete_terms' => '',
          'assign_terms' => 'edit_posts'
        )
         */
      )
    );
  } //if
  
} // oboxads_taxonomy_register()

