<?php

namespace BernskioldMedia\WP\Concierge\Tests;

use BernskioldMedia\WP\Concierge\Assets;
use BernskioldMedia\WP\Concierge\Plugin;

/**
 * @covers \BernskioldMedia\WP\Concierge\Plugin
 */
class PluginTest extends TestCase {

	public function test_instance_is_a_singleton(): void {
		$this->assertInstanceOf( Plugin::class, bm_concierge() );
		$this->assertSame( bm_concierge(), Plugin::instance() );
	}

	public function test_version_matches_plugin_header(): void {
		$header = get_file_data( BM_CONCIERGE_FILE_PATH, [ 'Version' => 'Version' ] );

		$this->assertSame( $header['Version'], Plugin::get_version() );
	}

	public function test_paths_and_urls_resolve_inside_the_plugin(): void {
		$this->assertSame( dirname( BM_CONCIERGE_FILE_PATH ) . '/dist/block-editor.js', Plugin::get_path( 'dist/block-editor.js' ) );
		$this->assertStringEndsWith( '/dist/block-editor.js', Plugin::get_url( 'dist/block-editor.js' ) );
		$this->assertFileExists( Plugin::get_path( 'dist/block-editor.asset.php' ) );
	}

	public function test_textdomain(): void {
		$this->assertSame( 'bm-concierge', Plugin::get_textdomain() );
	}

	public function test_hooks_are_registered(): void {
		$this->assertNotFalse( has_action( 'init', [ Plugin::class, 'load_languages' ] ) );
		$this->assertNotFalse( has_action( 'rest_api_init', [ Plugin::class, 'register_rest_routes' ] ) );
		$this->assertNotFalse( has_action( 'enqueue_block_editor_assets', [ Assets::class, 'block_editor' ] ) );
	}
}
