<?php
/**
 * Plugin Name:         BM Concierge
 * Plugin URI:          https://bernskioldmedia.com
 * Description:         Our help at your fingertips. BM Concierge lets you easily access content and assistive service for your site, right from the dashboard.
 * Version:             1.1.1
 * Requires at least:   6.8
 * Requires PHP:        8.1
 * Author:              Bernskiold Media
 * Author URI:          https://bernskioldmedia.com
 * Text Domain:         bm-concierge
 * Domain Path:         /languages/
 * Update URI:          false
 *
 * @package BernskioldMedia\WP\Concierge
 */

use BernskioldMedia\WP\Concierge\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader
 */
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

if ( file_exists( WP_CONTENT_DIR . '/vendor/autoload.php' ) ) {
	require_once WP_CONTENT_DIR . '/vendor/autoload.php';
}

/**
 * Basic Constants
 */
define( 'BM_CONCIERGE_FILE_PATH', __FILE__ );

/**
 * Initialize and boot the plugin.
 *
 * @return Plugin
 */
function bm_concierge(): Plugin {
	return Plugin::instance();
}

bm_concierge();

/**
 * Run update checker if not disabled.
 */
if ( ! defined( 'BM_CONCIERGE_DISABLE_UPDATER' ) || ( defined( 'BM_CONCIERGE_DISABLE_UPDATER' ) && false === BM_CONCIERGE_DISABLE_UPDATER ) ) {
	require_once bm_concierge()::get_path( 'lib/plugin-update-checker/plugin-update-checker.php' );

	$updater = Puc_v4_Factory::buildUpdateChecker( 'https://github.com/bernskiold/bm-concierge', __FILE__, 'bm-concierge' );
	$updater->getVcsApi()->enableReleaseAssets();
}
