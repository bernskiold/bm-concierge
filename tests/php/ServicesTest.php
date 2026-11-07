<?php

namespace BernskioldMedia\WP\Concierge\Tests;

use BernskioldMedia\WP\Concierge\Services\AccessibilityReview;
use BernskioldMedia\WP\Concierge\Services\Layout;
use BernskioldMedia\WP\Concierge\Services\MachineTranslation;
use BernskioldMedia\WP\Concierge\Services\OnPageOptimization;
use BernskioldMedia\WP\Concierge\Services\Proofreading;

/**
 * @covers \BernskioldMedia\WP\Concierge\Services\BaseService
 * @covers \BernskioldMedia\WP\Concierge\Services\Proofreading
 * @covers \BernskioldMedia\WP\Concierge\Services\AccessibilityReview
 * @covers \BernskioldMedia\WP\Concierge\Services\OnPageOptimization
 * @covers \BernskioldMedia\WP\Concierge\Services\Layout
 * @covers \BernskioldMedia\WP\Concierge\Services\MachineTranslation
 */
class ServicesTest extends TestCase {

	public function test_loads_the_matching_service_from_the_pricing_api(): void {
		$service = new Proofreading();

		$this->assertSame( 'proofreading', $service->data->name );
	}

	public function test_pricing_api_response_is_cached(): void {
		new Proofreading();
		new Layout();

		$this->assertSame( 1, $this->pricing_requests );
	}

	public function test_pricing_api_url_can_be_filtered(): void {
		$requested = null;

		add_filter( 'bm/concierge/pricing_api_url', function ( string $url, string $key ) {
			return "https://example.com/concierge/api.json?service={$key}";
		}, 10, 2 );

		add_filter( 'pre_http_request', function ( $response, $args, $url ) use ( &$requested ) {
			$requested = $url;

			return $response;
		}, 1, 3 );

		new Proofreading();

		$this->assertSame( 'https://example.com/concierge/api.json?service=proofreading', $requested );
	}

	public function test_failing_pricing_api_leaves_service_without_data(): void {
		$this->pricing_error = new \WP_Error( 'http_request_failed', 'Down' );

		$service = new Proofreading();

		$this->assertNull( $service->data );
		$this->assertFalse( get_transient( 'bm_concierge_products' ), 'A failed response must not be cached.' );
	}

	/**
	 * @dataProvider currency_provider
	 */
	public function test_currency_follows_locale( string $locale, string $currency ): void {
		add_filter( 'locale', fn() => $locale );

		$this->assertSame( $currency, ( new Proofreading() )->get_currency() );
	}

	public function currency_provider(): array {
		return [
			'Swedish' => [ 'sv_SE', 'SEK' ],
			'British' => [ 'en_GB', 'GBP' ],
			'US'      => [ 'en_US', 'USD' ],
			'Other'   => [ 'de_DE', 'USD' ],
		];
	}

	public function test_currency_can_be_filtered(): void {
		add_filter( 'bm/concierge/currency', function ( string $currency, string $key ) {
			return 'proofreading' === $key ? 'SEK' : $currency;
		}, 10, 2 );

		$this->assertSame( 'SEK', ( new Proofreading() )->get_currency() );
		$this->assertSame( 'USD', ( new Layout() )->get_currency() );
	}

	public function test_proofreading_cost(): void {
		$service = new Proofreading();

		$this->assertEqualsWithDelta( 50 + 1000 * 0.1, $service->get_cost( 1000 ), 0.001 );
		$this->assertEqualsWithDelta( 100 + 1000 * 0.2, $service->get_cost( 1000, true ), 0.001 );
	}

	public function test_accessibility_cost(): void {
		$service = new AccessibilityReview();

		$this->assertEquals( 200, $service->get_cost() );
		$this->assertEquals( 300, $service->get_cost( true ) );
	}

	public function test_onpage_cost_tiers(): void {
		$service = new OnPageOptimization();

		$this->assertEquals( 150, $service->get_cost( 499 ) );
		$this->assertEquals( 250, $service->get_cost( 500 ) );
		$this->assertEquals( 350, $service->get_cost( 500, true ) );
	}

	public function test_layout_cost_tiers(): void {
		$service = new Layout();

		$this->assertEquals( 100, $service->get_cost( 299 ) );
		$this->assertEquals( 200, $service->get_cost( 300 ) );
		$this->assertEquals( 300, $service->get_cost( 1000 ) );
		$this->assertEquals( 400, $service->get_cost( 1000, true ) );
	}

	public function test_machine_translation_cost(): void {
		$this->assertEqualsWithDelta( 50.0, ( new MachineTranslation() )->get_cost( 1000 ), 0.001 );
	}
}
