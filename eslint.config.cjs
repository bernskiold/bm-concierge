/**
 * ESLint flat config.
 *
 * ESLint 10 (shipped with recent @wordpress/scripts) no longer reads
 * `.eslintrc.*` or `.eslintignore`. The wp-scripts default config is reused
 * rather than depending on `@wordpress/eslint-plugin` directly, so the two can
 * never drift out of step.
 */
const defaultConfig = require( '@wordpress/scripts/config/eslint.config.cjs' );

module.exports = [
	{
		ignores: [
			'dist/**',
			'build/**',
			'node_modules/**',
			'vendor/**',
			'languages/**',
		],
	},

	...defaultConfig,

	{
		rules: {
			camelcase: [
				'error',
				{ properties: 'never', ignoreDestructuring: true },
			],
			'jsdoc/require-param-type': 'off',
			'jsdoc/require-returns-description': 'off',
		},
	},
];
