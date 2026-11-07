<?php

namespace BernskioldMedia\WP\Concierge;

defined( 'ABSPATH' ) || exit;

/**
 * Classes booted by the plugin implement a static hooks() method
 * that registers their actions and filters.
 */
interface Hookable {

	public static function hooks(): void;
}
