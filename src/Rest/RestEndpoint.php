<?php
/**
 * Abstract REST API Endpoint Class
 *
 * @package BernskioldMedia\WP\Concierge
 */

namespace BernskioldMedia\WP\Concierge\Rest;

use WP_REST_Controller;
use WP_REST_Request;

defined( 'ABSPATH' ) || exit;

abstract class RestEndpoint extends WP_REST_Controller {

	public const READABLE  = 'GET';
	public const CREATABLE = 'POST';
	public const EDITABLE  = 'PUT, POST, PATCH';
	public const DELETABLE = 'DELETE';
	public const ALL       = 'GET, POST, PUT, PATCH, DELETE';

	protected $namespace = 'wp_plugin_scaffold';

	protected string $version = '1';

	protected array $routes = [];

	/**
	 * Setup Extension
	 */
	public function load(): void {
		$this->setup_routes();
		$this->init();
	}

	public function init(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register the routes.
	 */
	abstract protected function setup_routes(): void;

	/**
	 * Register REST Routes
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_rest_route/
	 */
	public function register_routes(): void {
		foreach ( $this->get_routes() as $route => $args ) {
			register_rest_route( $this->get_namespace(), $route, $args );
		}
	}

	/**
	 * Get sanitized URL param filter value.
	 */
	protected function get_filter_value( WP_REST_Request $request, string $key ): ?string {
		return isset( $request[ $key ] ) ? wp_strip_all_tags( $request[ $key ] ) : null;
	}

	/**
	 * Add a route
	 *
	 * @return static
	 */
	protected function add_route( string $route, array $args ) {
		$this->routes[ $route ] = $args;

		return $this;
	}

	protected function get_routes(): array {
		return $this->routes;
	}

	protected function get_namespace(): string {
		return $this->namespace . '/v' . $this->version;
	}

	/**
	 * Public level permissions access.
	 */
	public function has_public_access(): bool {
		return true;
	}
}
