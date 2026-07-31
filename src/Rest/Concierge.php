<?php

namespace BernskioldMedia\WP\Concierge\Rest;

use BernskioldMedia\WP\Concierge\Services\AccessibilityReview;
use BernskioldMedia\WP\Concierge\Services\Layout;
use BernskioldMedia\WP\Concierge\Services\MachineTranslation;
use BernskioldMedia\WP\Concierge\Services\OnPageOptimization;
use BernskioldMedia\WP\Concierge\Services\Proofreading;
use DeepL\Translator;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

class Concierge extends RestEndpoint {

	protected $namespace = 'bm-concierge';
	protected string $version = '1';

	protected function setup_routes(): void {
		$this->add_route( '/services', [
			'methods'             => self::READABLE,
			'callback'            => [ $this, 'get_enabled_services' ],
			'permission_callback' => [ self::class, 'has_logged_in_access' ],
		] );

		$this->add_route( '/proofreading', [
			'methods'             => self::CREATABLE,
			'callback'            => [ $this, 'get_proofreading_data' ],
			'permission_callback' => [ self::class, 'has_logged_in_access' ],
		] );

		$this->add_route( '/accessibility', [
			'methods'             => self::CREATABLE,
			'callback'            => [ $this, 'get_accessibility_audit_data' ],
			'permission_callback' => [ self::class, 'has_logged_in_access' ],
		] );

		$this->add_route( '/onpage', [
			'methods'             => self::CREATABLE,
			'callback'            => [ $this, 'get_onpage_optimization_data' ],
			'permission_callback' => [ self::class, 'has_logged_in_access' ],
		] );

		$this->add_route( '/layout', [
			'methods'             => self::CREATABLE,
			'callback'            => [ $this, 'get_layout_data' ],
			'permission_callback' => [ self::class, 'has_logged_in_access' ],
		] );

		$this->add_route( '/machine_translation', [
			'methods'             => self::CREATABLE,
			'callback'            => [ $this, 'get_machine_translation_data' ],
			'permission_callback' => [ self::class, 'has_logged_in_access' ],
		] );

		$this->add_route( '/machine_translation_languages', [
			'methods'             => self::CREATABLE,
			'callback'            => [ $this, 'get_machine_translation_languages' ],
			'permission_callback' => [ self::class, 'has_logged_in_access' ],
		] );

		$this->add_route( '/order', [
			'methods'             => self::CREATABLE,
			'callback'            => [ $this, 'process_order' ],
			'permission_callback' => [ self::class, 'has_logged_in_access' ],
		] );
	}

	public function get_enabled_services(): WP_REST_Response {
		$data = [
			'proofreading'        => apply_filters( 'bm/concierge/services/proofreading', true ),
			'layout'              => apply_filters( 'bm/concierge/services/layout', true ),
			'accessibility'       => apply_filters( 'bm/concierge/services/accessibility', true ),
			'onpage'              => apply_filters( 'bm/concierge/services/onpage', true ),
			'machine_translation' => apply_filters( 'bm/concierge/services/machine_translation', true ) && defined( 'BM_CONCIERGE_ML_TRANSLATION_API_KEY' ),
		];

		return new WP_REST_Response( $data, 200 );
	}

	public function get_proofreading_data( WP_REST_Request $request ): WP_REST_Response {
		$body         = $request->get_json_params();
		$proofreading = new Proofreading();

		$data = [
			'currency'     => $proofreading->get_currency(),
			'expressPrice' => $proofreading->get_cost( (int) $body['wordCount'], true ),
			'normalPrice'  => $proofreading->get_cost( (int) $body['wordCount'], false ),
		];

		return new WP_REST_Response( $data, 200 );
	}

	public function get_accessibility_audit_data(): WP_REST_Response {
		$service = new AccessibilityReview();

		$data = [
			'currency'     => $service->get_currency(),
			'expressPrice' => $service->get_cost( true ),
			'normalPrice'  => $service->get_cost( false ),
		];

		return new WP_REST_Response( $data, 200 );
	}

	public function get_onpage_optimization_data( WP_REST_Request $request ): WP_REST_Response {
		$body    = $request->get_json_params();
		$service = new OnPageOptimization();

		$data = [
			'currency'     => $service->get_currency(),
			'expressPrice' => $service->get_cost( $body['wordCount'] ?? 0, true ),
			'normalPrice'  => $service->get_cost( $body['wordCount'] ?? 0, false ),
		];

		return new WP_REST_Response( $data, 200 );
	}

	public function get_layout_data( WP_REST_Request $request ): WP_REST_Response {
		$body    = $request->get_json_params();
		$service = new Layout();

		$data = [
			'currency'     => $service->get_currency(),
			'expressPrice' => $service->get_cost( $body['wordCount'] ?? 0, true ),
			'normalPrice'  => $service->get_cost( $body['wordCount'] ?? 0, false ),
		];

		return new WP_REST_Response( $data, 200 );
	}

	public function get_machine_translation_data( WP_REST_Request $request ): WP_REST_Response {
		$body    = $request->get_json_params();
		$service = new MachineTranslation();

		$data = [
			'currency'    => $service->get_currency(),
			'normalPrice' => $service->get_cost( $body['wordCount'] ?? 0 ),
		];

		return new WP_REST_Response( $data, 200 );
	}

	public function get_machine_translation_languages( WP_REST_Request $request ): WP_REST_Response {
		if ( ! defined( 'BM_CONCIERGE_ML_TRANSLATION_API_KEY' ) ) {
			return new WP_REST_Response( [], 401 );
		}

		$data = [
			'languages' => ( new Translator( BM_CONCIERGE_ML_TRANSLATION_API_KEY ) )->getTargetLanguages(),
		];

		return new WP_REST_Response( $data, 200 );
	}

	public static function has_logged_in_access(): bool {
		return is_user_logged_in();
	}

	public function process_order( WP_REST_Request $request ): WP_REST_Response {
		$data = $request->get_json_params();

		switch ( $data['type'] ?? null ) {
			case 'proofreading':
				$service = new Proofreading();
				break;

			case 'accessibility':
				$service = new AccessibilityReview();
				break;

			case 'onpage':
				$service = new OnPageOptimization();
				break;

			case 'layout':
				$service = new Layout();
				break;

			case 'machine_translation':
				$service = new MachineTranslation();
				break;

			default:
				return new WP_REST_Response( [], 401 );
		}

		$service->order( $data );

		return new WP_REST_Response( [], 200 );
	}
}
