/**
 * External dependencies
 */
import { describe, expect, it } from 'vitest';

/**
 * Internal dependencies
 */
import { parseMarkdown } from '../../assets/src/utilities/markdown';

describe( 'parseMarkdown', () => {
	it( 'converts headings', () => {
		expect( parseMarkdown( '# One\n## Two\n### Three' ) ).toBe(
			'<h1>One</h1>\n<h2>Two</h2>\n<h3>Three</h3>'
		);
	} );

	it( 'converts emphasis', () => {
		expect( parseMarkdown( '**bold** and *italic*' ) ).toBe(
			'<b>bold</b> and <i>italic</i>'
		);
	} );

	it( 'converts links and images', () => {
		expect( parseMarkdown( '[Docs](https://example.com)' ) ).toBe(
			"<a href='https://example.com'>Docs</a>"
		);
		expect( parseMarkdown( '![Logo](https://example.com/logo.png)' ) ).toBe(
			"<img alt='Logo' src='https://example.com/logo.png' />"
		);
	} );

	it( 'converts block quotes', () => {
		expect( parseMarkdown( '> Quote' ) ).toBe(
			'<blockquote>Quote</blockquote>'
		);
	} );
} );
