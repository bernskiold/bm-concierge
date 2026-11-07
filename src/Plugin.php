<?php

namespace BernskioldMedia\WP\Concierge;

defined( 'ABSPATH' ) || exit;

class Plugin {

	public const TEXTDOMAIN = 'bm-concierge';

	/**
	 * Classes with a static hooks() method to boot on load.
	 */
	private const BOOT = [
		Assets::class,
	];

	/**
	 * REST controllers to register.
	 */
	private const REST_ENDPOINTS = [
		Rest\Concierge::class,
	];

	protected static string $version = '1.1.1';

	private static ?self $instance = null;

	public static function instance(): self {
		return self::$instance ??= new self();
	}

	public function __construct() {
		register_activation_hook( BM_CONCIERGE_FILE_PATH, [ Install::class, 'install' ] );

		$this->init_hooks();

		do_action( 'bm_concierge_loaded' );
	}

	protected function init_hooks(): void {
		do_action( 'bm_concierge_init_hooks' );

		add_action( 'init', [ self::class, 'load_languages' ] );
		add_action( 'rest_api_init', [ self::class, 'register_rest_routes' ] );

		foreach ( self::BOOT as $class ) {
			$class::hooks();
		}
	}

	public static function register_rest_routes(): void {
		foreach ( self::REST_ENDPOINTS as $class ) {
			( new $class() )->register_routes();
		}
	}

	public static function get_path( string $file = '' ): string {
		return untrailingslashit( plugin_dir_path( BM_CONCIERGE_FILE_PATH ) ) . '/' . $file;
	}

	public static function get_url( string $file = '' ): string {
		return untrailingslashit( plugin_dir_url( BM_CONCIERGE_FILE_PATH ) ) . '/' . $file;
	}

	public static function get_version(): string {
		return static::$version;
	}

	public static function get_textdomain(): string {
		return self::TEXTDOMAIN;
	}

	public static function load_languages(): void {
		$textdomain = self::TEXTDOMAIN;
		$locale     = is_admin() ? get_user_locale() : get_locale();
		$locale     = apply_filters( 'plugin_locale', $locale, $textdomain );

		unload_textdomain( $textdomain );
		// Start checking in the main language dir, then fall back to the plugin.
		load_textdomain( $textdomain, WP_LANG_DIR . "/{$textdomain}/{$textdomain}-{$locale}.mo" );
		load_plugin_textdomain( $textdomain, false, dirname( plugin_basename( BM_CONCIERGE_FILE_PATH ) ) . '/languages' );
	}
}
