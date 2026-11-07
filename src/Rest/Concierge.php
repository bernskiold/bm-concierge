<?php

namespace BernskioldMedia\WP\Concierge\Rest;

use BernskioldMedia\WP\Concierge\Services\AccessibilityReview;
use BernskioldMedia\WP\Concierge\Services\Layout;
use BernskioldMedia\WP\Concierge\Services\MachineTranslation;
use BernskioldMedia\WP\Concierge\Services\OnPageOptimization;
use BernskioldMedia\WP\Concierge\Services\Proofreading;
use DeepL\Translator;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined( 'ABSPATH' ) || exit;

class Concierge extends WP_REST_Controller {

	/**
	 * Route namespace, including the version.
	 *
	 * @var string
	 */
	protected $namespace = 'bm-concierge/v1';

	public function register_routes(): void {
		$this->add_route( '/services', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_enabled_services' ],
			'permission_callback' => [ self::class, 'can_use_concierge' ],
		] );

		$this->add_route( '/proofreading', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'get_proofreading_data' ],
			'permission_callback' => [ self::class, 'can_use_concierge' ],
		] );

		$this->add_route( '/accessibility', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'get_accessibility_audit_data' ],
			'permission_callback' => [ self::class, 'can_use_concierge' ],
		] );

		$this->add_route( '/onpage', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'get_onpage_optimization_data' ],
			'permission_callback' => [ self::class, 'can_use_concierge' ],
		] );

		$this->add_route( '/layout', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'get_layout_data' ],
			'permission_callback' => [ self::class, 'can_use_concierge' ],
		] );

		$this->add_route( '/machine_translation', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'get_machine_translation_data' ],
			'permission_callback' => [ self::class, 'can_use_concierge' ],
		] );

		$this->add_route( '/machine_translation_languages', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'get_machine_translation_languages' ],
			'permission_callback' => [ self::class, 'can_use_concierge' ],
		] );

		$this->add_route( '/order', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'process_order' ],
			'permission_callback' => [ self::class, 'can_order_for_post' ],
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

	/**
	 * Register a route in this controller's namespace.
	 */
	protected function add_route( string $route, array $args ): void {
		register_rest_route( $this->namespace, $route, $args );
	}

	/**
	 * Concierge lives in the editor, so it is available to users who can
	 * edit posts.
	 */
	public static function can_use_concierge(): bool {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Orders act on a specific post (machine translation rewrites it), so the
	 * user must be able to edit that post.
	 *
	 * @return true|WP_Error
	 */
	public static function can_order_for_post( WP_REST_Request $request ) {
		if ( ! self::can_use_concierge() ) {
			return new WP_Error( 'rest_forbidden', __( 'Sorry, you are not allowed to do that.', 'bm-concierge' ), [ 'status' => rest_authorization_required_code() ] );
		}

		$data    = $request->get_json_params();
		$post_id = absint( $data['postId'] ?? 0 );

		if ( ! $post_id || ! get_post( $post_id ) ) {
			return new WP_Error( 'bm_concierge_invalid_post', __( 'A valid post is required to place an order.', 'bm-concierge' ), [ 'status' => 400 ] );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return new WP_Error( 'rest_forbidden', __( 'Sorry, you are not allowed to order services for this post.', 'bm-concierge' ), [ 'status' => rest_authorization_required_code() ] );
		}

		return true;
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
				return new WP_REST_Response( [], 400 );
		}

		$data['postId'] = absint( $data['postId'] );

		$service->order( $data );

		return new WP_REST_Response( [], 200 );
	}
}
