<?php
/**
 * Plugin Name:         BM Concierge
 * Plugin URI:          https://bernskioldmedia.com
 * Description:         Our help at your fingertips. BM Concierge lets you easily access content and assistive service for your site, right from the dashboard.
 * Version:             1.1.1
 * Requires at least:   7.0
 * Tested up to:        7.1
 * Requires PHP:        8.2
 * Author:              Bernskiold Media
 * Author URI:          https://bernskioldmedia.com
 * Text Domain:         bm-concierge
 * Domain Path:         /languages/
 * Update URI:          false
 *
 * @package BernskioldMedia\WP\Concierge
 */

use BernskioldMedia\WP\Concierge\Plugin;
use YahnisElsts\PluginUpdateChecker\v6\PucFactory;

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
 * Update the plugin from the ZIP attached to each GitHub release, unless
 * BM_CONCIERGE_DISABLE_UPDATER is true. The update checker is a Composer
 * dependency, shipped in the release ZIP's vendor/.
 */
if ( ( ! defined( 'BM_CONCIERGE_DISABLE_UPDATER' ) || false === BM_CONCIERGE_DISABLE_UPDATER ) && class_exists( PucFactory::class ) ) {
	$bm_concierge_updater = PucFactory::buildUpdateChecker( 'https://github.com/bernskiold/bm-concierge', __FILE__, 'bm-concierge' );
	$bm_concierge_updater->getVcsApi()->enableReleaseAssets();
}
