const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const WooCommerceDependencyExtractionWebpackPlugin = require( '@woocommerce/dependency-extraction-webpack-plugin' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );

// Remove SASS rule from the default config so we can define our own.
const defaultRules = defaultConfig.module.rules.filter( ( rule ) => {
	return String( rule.test ) !== String( /\.(sc|sa)ss$/ );
} );

module.exports = {
	...defaultConfig,
	entry: {
		index: path.resolve( process.cwd(), 'src', 'js', 'index.js' ),
		'correosoficial-wc-block': path.resolve(
			process.cwd(),
			'src',
			'js',
			'correosoficial-wc-block',
			'index.js'
		),
		'correosoficial-wc-block-frontend': path.resolve(
			process.cwd(),
			'src',
			'js',
			'correosoficial-wc-block',
			'frontend.js'
		),
		'correosoficial-wc-block-nif': path.resolve(
			process.cwd(),
			'src',
			'js',
			'correosoficial-wc-block-nif',
			'index.js'
		),
		'correosoficial-wc-block-nif-frontend': path.resolve(
			process.cwd(),
			'src',
			'js',
			'correosoficial-wc-block-nif',
			'frontend.js'
		),
	},
	module: {
		...defaultConfig.module,
		rules: [
			...defaultRules,
			{
				test: /\.(sc|sa)ss$/,
				exclude: /node_modules/,
				use: [
					MiniCssExtractPlugin.loader,
					{ loader: 'css-loader', options: { importLoaders: 1 } },
					{
						loader: 'sass-loader',
						options: {
							sassOptions: {
								includePaths: [ 'src/css' ],
							},
						},
					},
				],
			},
		],
	},
	plugins: [
		...defaultConfig.plugins.filter(
			( plugin ) =>
				plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
		),
		new WooCommerceDependencyExtractionWebpackPlugin(),
		new MiniCssExtractPlugin( {
			filename: `[name].css`,
		} ),
	],
};