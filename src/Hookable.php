<?php
/**
 * Hookable
 *
 * @package BernskioldMedia\WP\Concierge
 */

namespace BernskioldMedia\WP\Concierge;

defined( 'ABSPATH' ) || exit;

interface Hookable {

	/**
	 * Hookable classes must implement a standardized hooks function
	 * that can be called when booted.
	 */
	public static function hooks(): void;
}
