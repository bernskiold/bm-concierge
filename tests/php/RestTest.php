<?php

namespace BernskioldMedia\WP\Concierge\Tests;

use WP_REST_Request;
use WP_REST_Server;

/**
 * @covers \BernskioldMedia\WP\Concierge\Rest\Concierge
 */
class RestTest extends TestCase {

	private WP_REST_Server $server;

	public function set_up(): void {
		parent::set_up();

		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init', $this->server );

		reset_phpmailer_instance();
	}

	public function tear_down(): void {
		global $wp_rest_server;
		$wp_rest_server = null;

		parent::tear_down();
	}

	private function request( string $method, string $route, array $body = [] ) {
		$request = new WP_REST_Request( $method, '/bm-concierge/v1' . $route );

		if ( $body ) {
			$request->set_header( 'Content-Type', 'application/json' );
			$request->set_body( wp_json_encode( $body ) );
		}

		return $this->server->dispatch( $request );
	}

	private function log_in(): \WP_User {
		$user = self::factory()->user->create_and_get( [
			'role'       => 'editor',
			'first_name' => 'Ada',
			'last_name'  => 'Lovelace',
			'user_email' => 'ada@example.com',
		] );

		wp_set_current_user( $user->ID );

		return $user;
	}

	public function test_routes_are_registered(): void {
		$routes = $this->server->get_routes( 'bm-concierge/v1' );

		foreach ( [ 'services', 'proofreading', 'accessibility', 'onpage', 'layout', 'machine_translation', 'machine_translation_languages', 'order' ] as $route ) {
			$this->assertArrayHasKey( "/bm-concierge/v1/{$route}", $routes );
		}
	}

	public function test_routes_require_a_logged_in_user(): void {
		wp_set_current_user( 0 );

		$this->assertSame( 401, $this->request( 'GET', '/services' )->get_status() );
		$this->assertSame( 401, $this->request( 'POST', '/order', [ 'type' => 'proofreading' ] )->get_status() );
	}

	public function test_services_are_enabled_by_default_except_machine_translation_without_key(): void {
		$this->log_in();

		$response = $this->request( 'GET', '/services' );

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( [
			'proofreading'        => true,
			'layout'              => true,
			'accessibility'       => true,
			'onpage'              => true,
			'machine_translation' => false,
		], $response->get_data() );
	}

	public function test_services_can_be_disabled_with_filters(): void {
		$this->log_in();
		add_filter( 'bm/concierge/services/layout', '__return_false' );

		$this->assertFalse( $this->request( 'GET', '/services' )->get_data()['layout'] );
	}

	public function test_proofreading_prices(): void {
		$this->log_in();

		$response = $this->request( 'POST', '/proofreading', [ 'wordCount' => 1000 ] );

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 'USD', $response->get_data()['currency'] );
		$this->assertEqualsWithDelta( 150, $response->get_data()['normalPrice'], 0.001 );
		$this->assertEqualsWithDelta( 300, $response->get_data()['expressPrice'], 0.001 );
	}

	public function test_layout_prices(): void {
		$this->log_in();

		$data = $this->request( 'POST', '/layout', [ 'wordCount' => 400 ] )->get_data();

		$this->assertEquals( 200, $data['normalPrice'] );
		$this->assertEquals( 300, $data['expressPrice'] );
	}

	public function test_machine_translation_languages_require_api_key(): void {
		$this->log_in();

		$this->assertSame( 401, $this->request( 'POST', '/machine_translation_languages' )->get_status() );
	}

	public function test_unknown_order_type_is_rejected(): void {
		$this->log_in();

		$response = $this->request( 'POST', '/order', [ 'type' => 'nope' ] );

		$this->assertNotSame( 200, $response->get_status() );
		$this->assertSame( 0, did_action( 'phpmailer_init' ) );
	}

	public function test_order_emails_support_and_the_customer(): void {
		$this->log_in();
		$post_id = self::factory()->post->create( [ 'post_title' => 'About Us' ] );

		$response = $this->request( 'POST', '/order', [
			'type'    => 'proofreading',
			'postId'  => $post_id,
			'express' => true,
			'price'   => '$300',
		] );

		$this->assertSame( 200, $response->get_status() );

		$mailer   = tests_retrieve_phpmailer_instance();
		$internal = $mailer->get_sent( 0 );
		$customer = $mailer->get_sent( 1 );

		$this->assertSame( 'support@bernskioldmedia.com', $internal->to[0][0] );
		$this->assertStringContainsString( 'New Proofreading Concierge Order', $internal->subject );
		$this->assertStringContainsString( 'About Us', $internal->body );
		$this->assertStringContainsString( 'Express', $internal->body );

		$this->assertSame( 'ada@example.com', $customer->to[0][0] );
		$this->assertStringContainsString( 'Hi Ada,', $customer->body );
		$this->assertStringContainsString( '$300', $customer->body );
	}
}
