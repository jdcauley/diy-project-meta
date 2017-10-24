<?php
/**
 * Class SampleTest
 *
 * @package Diwhy
 */

/**
 * Sample test case.
 */
class Extend_API_Test extends WP_UnitTestCase {

  private $plugin;

  protected $namespaced_route = '/diy_meta_plugin/v1';

  function setup() {

    parent::setUp();

    global $wp_rest_server;

    $this->server = $wp_rest_server = new \WP_REST_Server;
    $this->nonce = wp_create_nonce( 'wp_rest' );
    do_action( 'rest_api_init' );

  }

  public function tearDown() {
    unset($_REQUEST['security']);
  }

  /* Check for Custom Routes */
  public function test_register_route() {

    $routes = $this->server->get_routes();
    $this->assertArrayHasKey( $this->namespaced_route, $routes );
    $this->assertArrayHasKey( $this->namespaced_route . '/diy_meta_difficulty', $routes);
    $this->assertArrayHasKey( $this->namespaced_route . '/diy_meta_difficulty/(?P<id>\d+)', $routes);

  }

  public function test_endpoints() {

    $the_route = $this->namespaced_route;
    $routes = $this->server->get_routes();

    foreach( $routes as $route => $route_config ) {
      if( 0 === strpos( $the_route, $route ) ) {
        $this->assertTrue( is_array( $route_config ) );
        foreach( $route_config as $i => $endpoint ) {
          $this->assertArrayHasKey( 'callback', $endpoint );
          $this->assertArrayHasKey( 0, $endpoint[ 'callback' ], get_class( $this ) );
          $this->assertArrayHasKey( 1, $endpoint[ 'callback' ], get_class( $this ) );
          $this->assertTrue( is_callable( array( $endpoint[ 'callback' ][0], $endpoint[ 'callback' ][1] ) ) );
        }
      }
    }

  }

  /* Test Custom Endpoint Security */
  public function test_responses() {

    $request = new WP_REST_Request( 'GET', $this->namespaced_route . '/diy_meta_difficulty' );
    
    $response = $this->server->dispatch( $request );
    $this->assertEquals( 403, $response->get_status() );

    $request = new WP_REST_Request( 'POST', $this->namespaced_route . '/diy_meta_difficulty' );
    $request->set_body('');

    $response = $this->server->dispatch( $request );
    $this->assertEquals( 403, $response->get_status() );

    $request = new WP_REST_Request( 'DELETE', $this->namespaced_route . '/diy_meta_difficulty/5' );

    $response = $this->server->dispatch( $request );
    $this->assertEquals( 403, $response->get_status() );

  }

}
