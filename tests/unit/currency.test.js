/**
 * External dependencies
 */
import { describe, expect, it } from 'vitest';

/**
 * Internal dependencies
 */
import {
	formatCurrency,
	getLocaleForCurrency,
} from '../../assets/src/utilities/currency';

describe( 'getLocaleForCurrency', () => {
	it( 'uses Swedish formatting for SEK', () => {
		expect( getLocaleForCurrency( 'SEK' ) ).toBe( 'sv-SE' );
	} );

	it( 'falls back to US English formatting', () => {
		expect( getLocaleForCurrency( 'USD' ) ).toBe( 'en-US' );
		expect( getLocaleForCurrency( 'GBP' ) ).toBe( 'en-US' );
	} );
} );

describe( 'formatCurrency', () => {
	it( 'returns the raw value when no currency is known yet', () => {
		expect( formatCurrency( 150, null ) ).toBe( 150 );
	} );

	it( 'formats whole amounts in the given currency', () => {
		expect( formatCurrency( 1500.4, 'USD' ) ).toBe( '$1,500' );
		expect( formatCurrency( 99, 'GBP' ) ).toBe( '£99' );
	} );

	it( 'formats SEK the Swedish way', () => {
		// Intl uses a non-breaking space between the groups and the unit.
		expect( formatCurrency( 1500, 'SEK' ).replace( /\s/g, ' ' ) ).toBe(
			'1 500 kr'
		);
	} );
} );
