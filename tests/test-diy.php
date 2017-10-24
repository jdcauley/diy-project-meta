<?php

  class DIY_Test extends WP_UnitTestCase {

    function setUP() {

      parent::setUp();

      $this->DIY = new DIY_Project_Meta();

      $this->DIY->plugin_activation();

    }

    function tearDown() {
      $this->DIY->plugin_deactivation();
    }

    function test_term_created() {

      $terms = get_taxonomies();

      $this->assertArrayHasKey('diy_meta_difficulty', $terms);

    }

    function test_term_defaults() {

      $terms = get_terms( array(
        'taxonomy' => 'diy_meta_difficulty',
        'hide_empty' => false,
        'orderby' => 'term_id', 
        'order' => 'ASC'
      ) );

      $this->assertEquals(count($terms), 5);

      foreach ($terms as $term) {
        $this->assertContains($term->name, array(
          'Very Easy', 'Easy', 'Moderate', 'Hard', 'Very Hard'
          )
        );
      }

    }

  }