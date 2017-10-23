<?php

  class DIY_Rest extends DIY_Project_Meta {

    const API_VERSION = 'v1';

    function __construct () {

      add_action( 'rest_api_init' , array( $this, 'create_rest_routes' ) );

    }

    function delete_custom_term ( WP_REST_Request $request ) {

      $params = $request->get_params();

      $res_data = array();

      $res_data['term_removed'] = wp_delete_term( $params['id'], self::PREFIX . 'difficulty' );

      $response = new WP_REST_Response( $res_data );

      if ( $res_data['term_removed'] ) {
        $response->set_status( 200 );
      } else {
        $response->set_status( 400 );
      }
      
      return $response;
    }

    function create_custom_term ( WP_REST_Request $request ) {

      $params = $request->get_params();

      $term_name = sanitize_text_field( $params['diy_meta_new_term'] );
      $new_term = wp_insert_term( $term_name, self::PREFIX . 'difficulty', array() );

      if ( is_wp_error($new_term) ) {
        $response = new WP_REST_Response( $new_term );
        $response->set_status( 500 );
        return $response;
      }

      $res_data = get_term($new_term['term_id'], self::PREFIX . 'difficulty');
      
      $response = new WP_REST_Response( $res_data );
      $response->set_status( 201 );

      return $response;

    }

    function get_custom_terms ( WP_REST_Request $request ) {

      $terms = get_terms( array(
        'taxonomy' => self::PREFIX . 'difficulty',
        'hide_empty' => false,
        'orderby' => 'term_id', 
        'order' => 'ASC'
      ) );

      if ( is_wp_error($terms) ) {
        $response = new WP_REST_Response( $terms );
        $response->set_status( 500 );
        return $response;
      }

      $response = new WP_REST_Response( $terms );
      $response->set_status( 200 );

      return $response;
    }

    function create_rest_routes () {

      register_rest_route(
        self::PLUGIN_DOMAIN . '/' . self::API_VERSION, 
        '/' . self::PREFIX . 'difficulty/', 
        array(
          'methods' => 'GET',
          'callback' => array( $this, 'get_custom_terms' ),
          'permission_callback' => function () {
            return current_user_can( 'manage_options' );
          }
        )
      );

      register_rest_route(
        self::PLUGIN_DOMAIN . '/' . self::API_VERSION, 
        '/' . self::PREFIX . 'difficulty/(?P<id>\d+)', 
        array(
          'methods' => 'DELETE',
          'callback' => array( $this, 'delete_custom_term' ),
          'permission_callback' => function () {
            return current_user_can( 'manage_options' );
          }
        )
      );

      register_rest_route(
        self::PLUGIN_DOMAIN . '/' . self::API_VERSION, 
        '/' . self::PREFIX . 'difficulty/', 
        array(
          'methods' => 'POST',
          'callback' => array( $this, 'create_custom_term' ),
          'permission_callback' => function () {
            return current_user_can( 'manage_options' );
          }
        )
      );
    }

  }