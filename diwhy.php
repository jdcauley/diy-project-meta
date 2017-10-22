<?php

/* 
  Plugin Name: DIY Project Meta
  Description: Information on DIY Projects
  Author: Jordan Cauley
*/

class DIY_Project_Meta {

  /*
   * Version
   * @var string
   */
  const VERSION = '1.0.0';

  const TEXT_DOMAIN = 'diy_meta_plugin';

  const PLUGIN_DOMAIN = 'diy_meta_plugin';

  const PREFIX = 'diy_meta_';

  function __construct () {

    add_action( 'init', array($this, 'add_difficulty_term') );

  }

  public static function assets_url () {
    return plugin_dir_url( __FILE__ );
  }

  function add_difficulty_term () {

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
    );

    register_taxonomy( self::PREFIX . 'difficulty', array('post'), $term );

  }

}

require_once('includes/diy-settings.php');

$diy = new DIY_Project_Meta();
$settings = new DIY_Settings();