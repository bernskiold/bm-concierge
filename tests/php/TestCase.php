<?php

namespace BernskioldMedia\WP\Concierge\Tests;

use WP_UnitTestCase;

/**
 * Base test case that serves the pricing API from a local fixture, so no
 * test ever reaches static.bernskioldmedia.com.
 */
abstract class TestCase extends WP_UnitTestCase {

	/**
	 * Number of requests made to the pricing API during the test.
	 */
	protected int $pricing_requests = 0;

	/**
	 * When set, the pricing API responds with this error instead of the fixture.
	 */
	protected ?\WP_Error $pricing_error = null;

	public function set_up(): void {
		parent::set_up();

		delete_transient( 'bm_concierge_products' );
		$this->pricing_requests = 0;
		$this->pricing_error    = null;

		add_filter( 'pre_http_request', [ $this, 'mock_pricing_api' ], 10, 3 );
	}

	public function tear_down(): void {
		remove_filter( 'pre_http_request', [ $this, 'mock_pricing_api' ], 10 );
		delete_transient( 'bm_concierge_products' );

		parent::tear_down();
	}

	/**
	 * @param false|array|\WP_Error $response
	 */
	public function mock_pricing_api( $response, array $args, string $url ) {
		if ( false === strpos( $url, 'concierge/api.json' ) ) {
			return $response;
		}

		++$this->pricing_requests;

		if ( $this->pricing_error ) {
			return $this->pricing_error;
		}

		return [
			'headers'  => [],
			'body'     => file_get_contents( __DIR__ . '/Fixtures/pricing.json' ),
			'response' => [
				'code'    => 200,
				'message' => 'OK',
			],
			'cookies'  => [],
			'filename' => null,
		];
	}
}
