<?php
/**
 * Installer
 *
 * @package BernskioldMedia\WP\Concierge
 */

namespace BernskioldMedia\WP\Concierge;

defined( 'ABSPATH' ) || exit;

class Install {

	public static function install(): void {
		do_action( 'bm_concierge_install' );
	}
}
