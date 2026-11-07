/**
 * Webpack configuration.
 *
 * Extends the default @wordpress/scripts config. The plugin keeps its PHP
 * classes in `src/`, which is the wp-scripts default source directory, so the
 * editor entry point and output directory are set explicitly here.
 */
const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,
	entry: {
		'block-editor': './assets/src/block-editor.js',
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( __dirname, 'dist' ),
	},
};
