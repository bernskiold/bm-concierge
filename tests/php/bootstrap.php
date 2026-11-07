<?php
/**
 * PHPUnit bootstrap for the WordPress integration test suite.
 *
 * Designed to run inside `wp-env`'s `tests-cli` container, which provides the
 * WordPress test library and sets `WP_TESTS_DIR`. Run with:
 *
 *   npm run env:start
 *   npm run test:php
 *
 * @package BernskioldMedia\WP\Concierge
 */

// Locate the WordPress test library. wp-env exposes it via WP_TESTS_DIR;
// fall back to the more generic WP_PHPUNIT__DIR and finally the system temp dir.
$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = getenv( 'WP_PHPUNIT__DIR' );
}

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Point the WP test suite at the Yoast PHPUnit polyfills bundled via Composer.
if ( ! defined( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' ) ) {
	$_polyfills = dirname( __DIR__, 2 ) . '/vendor/yoast/phpunit-polyfills';

	if ( is_dir( $_polyfills ) ) {
		define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_polyfills );
	}
}

$_functions = "{$_tests_dir}/includes/functions.php";

if ( ! file_exists( $_functions ) ) {
	echo "Could not find the WordPress test library at {$_tests_dir}." . PHP_EOL;
	echo 'Start the test environment with `npm run env:start` and run tests via `npm run test:php`.' . PHP_EOL;
	exit( 1 );
}

// Give access to tests_add_filter() before WordPress loads.
require_once $_functions;

/**
 * Manually load the plugin under test.
 */
function _manually_load_bm_concierge(): void {
	// The update checker talks to GitHub; keep the test run offline.
	if ( ! defined( 'BM_CONCIERGE_DISABLE_UPDATER' ) ) {
		define( 'BM_CONCIERGE_DISABLE_UPDATER', true );
	}

	require dirname( __DIR__, 2 ) . '/bm-concierge.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_bm_concierge' );

// Start up the WP testing environment.
require "{$_tests_dir}/includes/bootstrap.php";
