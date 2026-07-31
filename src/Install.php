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
		if ( method_exists( static::class, 'scheduled_tasks' ) ) {
			static::scheduled_tasks();
		}

		flush_rewrite_rules();

		do_action( 'bm_concierge_install' );
	}

}
