<?php

/* 
  Plugin Name: DIY Project Meta
  Description: Set DIY Project info for Posts.
  Author: Jordan Cauley
  Version: 1.0.0
*/

class DIY_Project_Meta {

  const VERSION = '1.0.0';

  const TEXT_DOMAIN = 'diy_meta_plugin';

  const PLUGIN_DOMAIN = 'diy_meta_plugin';

  const PREFIX = 'diy_meta_';

  function __construct () {

    add_action( 'init', array($this, 'add_difficulty_term') );

    $plugin_hook = 'plugin_action_links_' . plugin_basename( __FILE__ );
    add_filter( $plugin_hook, array($this, 'plugin_add_settings_link' ) );

    register_activation_hook( __FILE__, array($this, 'plugin_activation') );

  }

  public static function assets_url () {
    return plugin_dir_url( __FILE__ );
  }

  function plugin_add_settings_link ( $links ) {
    $settings_link = '<a href="admin.php?page=' . self::PLUGIN_DOMAIN . '">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
    return $links;
  }

  public static function add_difficulty_term () {

    $labels = array(
      'name'              => _x( 'Difficulty', 'taxonomy general name', self::TEXT_DOMAIN ),
      'singular_name'     => _x( 'Difficulty', 'taxonomy singular name', self::TEXT_DOMAIN ),
      'search_items'      => __( 'Search Difficulty', self::TEXT_DOMAIN ),
      'all_items'         => __( 'All Difficulty', self::TEXT_DOMAIN ),
      'parent_item'       => __( 'Parent Difficulty', self::TEXT_DOMAIN ),
      'parent_item_colon' => __( 'Parent Difficulty:', self::TEXT_DOMAIN ),
      'edit_item'         => __( 'Edit Difficulty', self::TEXT_DOMAIN ),
      'update_item'       => __( 'Update Difficulty', self::TEXT_DOMAIN ),
      'add_new_item'      => __( 'Add New Difficulty', self::TEXT_DOMAIN ),
      'new_item_name'     => __( 'New Difficulty Name', self::TEXT_DOMAIN ),
      'menu_name'         => __( 'Difficulty', self::TEXT_DOMAIN ),
    );

    $term = array (
      'hierarchical'      => true,
      'labels'            => $labels,
      'show_ui'           => false,
      'show_admin_column' => true,
      'query_var'         => true,
      'show_in_rest'      => true
    );

    register_taxonomy( self::PREFIX . 'difficulty', array('post'), $term );

  }

  public static function plugin_activation () {
    /* Create Baseline Difficulty Options */
    self::add_difficulty_term();

    $terms = get_terms( array(
      'taxonomy' => self::PREFIX . 'difficulty',
      'hide_empty' => false
    ) );

    if ( count($terms) > 0) {
      return;
    }

    $default_difficulties = array(
      'Very Easy', 'Easy', 'Moderate', 'Hard', 'Very Hard'
    );
    
    foreach ( $default_difficulties as $level ) {
      $term = wp_insert_term( $level, self::PREFIX . 'difficulty', array() );
    }

  }

}

require_once('includes/plugin-settings.php');
require_once('includes/post-settings.php');
require_once('includes/extend-api.php');

$diy = new DIY_Project_Meta();
$settings = new DIY_Settings();
$post_settings = new DIY_Post_Meta();
$extend_rest = new DIY_Rest();
