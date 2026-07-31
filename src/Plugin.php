<?php

namespace BernskioldMedia\WP\Concierge;

use WP_CLI;

defined( 'ABSPATH' ) || exit;

class Plugin {

	/**
	 * A machine readable plugin slug, used to automatically prefix certain actions.
	 */
	protected static string $slug = 'bm_concierge';

	protected static string $version = '1.1.1';

	/**
	 * Database Version
	 */
	protected static string $database_version = '1000';

	protected static string $textdomain = 'bm-concierge';

	protected static string $plugin_file_path = BM_CONCIERGE_FILE_PATH;

	/**
	 * Classes booted when the plugin runs.
	 *
	 * @var array<class-string<Hookable>>
	 */
	protected static array $boot = [
		Assets::class,
	];

	/**
	 * REST endpoints loaded alongside the plugin.
	 *
	 * @var array<class-string<Rest\RestEndpoint>>
	 */
	protected static array $rest_endpoints = [
		Rest\Concierge::class,
	];

	protected static ?self $instance = null;

	public static function instance(): self {
		return self::$instance ??= new self();
	}

	public function __construct() {
		$this->init_hooks();

		do_action( self::$slug . '_loaded' );
	}

	protected function init_hooks(): void {
		do_action( self::$slug . '_init_hooks' );

		add_action( 'init', [ self::class, 'load_languages' ] );

		foreach ( self::$boot as $bootable_class ) {
			$bootable_class::hooks();
		}

		foreach ( self::$rest_endpoints as $endpoint ) {
			( new $endpoint() )->load();
		}

		add_action( 'cli_init', static function () {
			WP_CLI::add_command( 'bm-concierge', Cli::class );
		} );
	}

	/**
	 * Load plugin translations.
	 */
	public static function load_languages(): void {
		$textdomain = self::get_textdomain();
		$locale     = is_admin() ? get_user_locale() : get_locale();
		$locale     = apply_filters( 'plugin_locale', $locale, $textdomain );

		unload_textdomain( $textdomain );

		// Start checking in the main language dir.
		load_textdomain( $textdomain, WP_LANG_DIR . '/' . $textdomain . '/' . $textdomain . '-' . $locale . '.mo' );

		// Otherwise, load from the plugin.
		load_plugin_textdomain( $textdomain, false, dirname( plugin_basename( self::$plugin_file_path ) ) . '/languages' );
	}

	/**
	 * Get the path to the plugin folder, or the specified
	 * file relative to the plugin folder home.
	 */
	public static function get_path( string $file = '' ): string {
		return untrailingslashit( plugin_dir_path( self::$plugin_file_path ) ) . '/' . $file;
	}

	/**
	 * Get the URL to the plugin folder, or the specified
	 * file relative to the plugin folder home.
	 */
	public static function get_url( string $file = '' ): string {
		return untrailingslashit( plugin_dir_url( self::$plugin_file_path ) ) . '/' . $file;
	}

	/**
	 * Get the URL to the assets folder, or the specified
	 * file relative to the assets folder home.
	 */
	public static function get_assets_url( string $file = '' ): string {
		return self::get_url( 'assets/' . $file );
	}

	public static function get_ajax_url(): string {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	public static function get_version(): string {
		return self::$version;
	}

	public static function get_database_version(): string {
		return self::$database_version;
	}

	public static function get_textdomain(): string {
		return self::$textdomain;
	}
}
