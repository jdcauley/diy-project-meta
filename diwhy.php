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

  function __construct () {

    add_action( 'add_meta_boxes', array($this, 'meta_boxes') );

  }

  function difficulty_box ( $post ) {

  }

  function meta_boxes () {

    add_meta_box( 'diy_project_meta_difficulty', __('Project Difficulty', 'diy_project_meta' ), array($this, 'difficulty_box'), 'post' );


  }


}

$diy = new DIY_Project_Meta();