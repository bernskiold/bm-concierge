/**
 * External dependencies
 */
import { beforeEach, describe, expect, it, vi } from 'vitest';

/**
 * WordPress dependencies
 */
import { createRegistry } from '@wordpress/data';

/**
 * Internal dependencies
 */
import conciergeStore from '../../assets/src/store/concerige';

vi.mock( '@wordpress/api-fetch', () => ( { default: vi.fn() } ) );

describe( 'bm/concierge store', () => {
	let registry;

	beforeEach( () => {
		registry = createRegistry();
		registry.register( conciergeStore );
	} );

	it( 'stores prices per service', () => {
		registry
			.dispatch( conciergeStore )
			.setPrices( 'layout', 300, 200, 'SEK' );
		registry
			.dispatch( conciergeStore )
			.setPrices( 'onpage', 350, 250, 'SEK' );

		const { prices } = registry.select( conciergeStore ).getPrices();

		expect( prices ).toEqual( {
			layout: { express: 300, normal: 200, currency: 'SEK' },
			onpage: { express: 350, normal: 250, currency: 'SEK' },
		} );
	} );

	it( 'stores services and supported languages', () => {
		registry
			.dispatch( conciergeStore )
			.setServices( { layout: true, onpage: false } );
		registry
			.dispatch( conciergeStore )
			.setSupportedLanguages( [ { code: 'sv' } ] );

		expect( registry.select( conciergeStore ).getServices() ).toEqual( {
			services: { layout: true, onpage: false },
		} );
		expect( registry.select( conciergeStore ).getLanguages() ).toEqual( {
			languages: [ { code: 'sv' } ],
		} );
	} );
} );
