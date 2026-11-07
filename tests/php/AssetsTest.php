<?php

namespace BernskioldMedia\WP\Concierge\Tests;

use BernskioldMedia\WP\Concierge\Assets;

/**
 * @covers \BernskioldMedia\WP\Concierge\Assets
 */
class AssetsTest extends TestCase {

	public function tear_down(): void {
		wp_dequeue_script( 'bm-concierge-block-editor' );
		wp_deregister_script( 'bm-concierge-block-editor' );
		wp_dequeue_style( 'bm-concierge-block-editor' );
		wp_deregister_style( 'bm-concierge-block-editor' );

		parent::tear_down();
	}

	public function test_enqueues_editor_script_with_built_dependencies(): void {
		Assets::block_editor();

		$this->assertTrue( wp_script_is( 'bm-concierge-block-editor', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'bm-concierge-block-editor', 'enqueued' ) );

		$script = wp_scripts()->registered['bm-concierge-block-editor'];
		$this->assertContains( 'wp-editor', $script->deps );
		$this->assertContains( 'wp-plugins', $script->deps );
	}

	public function test_enqueuing_twice_keeps_dependencies(): void {
		Assets::block_editor();
		wp_deregister_script( 'bm-concierge-block-editor' );
		Assets::block_editor();

		$this->assertNotEmpty( wp_scripts()->registered['bm-concierge-block-editor']->deps );
	}

	public function test_can_be_disabled_with_filter(): void {
		add_filter( 'bm/concierge/enabled', '__return_false' );

		Assets::block_editor();

		$this->assertFalse( wp_script_is( 'bm-concierge-block-editor', 'registered' ) );
	}
}
