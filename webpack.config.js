const DEV = 'production' !== process.env.NODE_ENV;

/**
* NPM Plugins.
*/
const path                    = require( 'path' );
const MiniCssExtractPlugin    = require( 'mini-css-extract-plugin' );
const OptimizeCssAssetsPlugin = require( 'optimize-css-assets-webpack-plugin' );
const cssnano                 = require( 'cssnano' );
const CleanWebpackPlugin      = require( 'clean-webpack-plugin' );
const TerserPlugin            = require( 'terser-webpack-plugin' );
const StyleLintPlugin         = require( 'stylelint-webpack-plugin' );
const FriendlyErrorsPlugin    = require( 'friendly-errors-webpack-plugin' );

// Assets Directory path.
const JSDir     = path.resolve( __dirname, 'assets/admin/js' );
const SCSSDir   = path.resolve( __dirname, 'assets/admin/scss' );
const Assets    = path.resolve( __dirname, 'assets/admin' );
const BUILD_DIR = path.resolve( __dirname, 'assets/admin/build' );

// Entry points
const entry = {
	'pto-main': [ Assets + '/pto-main.js' ]
};

// Outputs
const output = {
	path: BUILD_DIR,
	filename: DEV ? 'js/[name].js' : 'js/[name].min.js',
	sourceMapFilename: '[name].js.map'
};

const plugins = ( argv ) => [
	new CleanWebpackPlugin( [ BUILD_DIR ] ),

	new MiniCssExtractPlugin( {
		filename: DEV ? '[name].css' : '[name].min.css'
	} ),

	new StyleLintPlugin( {
		'extends': 'stylelint-config-wordpress/scss'
	} ),

	new FriendlyErrorsPlugin( {
		clearConsole: false
	} )
];

const rules = [
	{
		enforce: 'pre',
		test: /\.js$/,
		exclude: /node_modules/,
		use: [ 'eslint-loader', 'source-map-loader' ]
	},
	{
		test: /\.js$/,
		include: [ JSDir ],
		exclude: /node_modules/,
		use: 'babel-loader'
	},
	{
		test: /\.(eot|woff|woff2|svg|ttf)([\?]?.*)$/,
		use: [
			{
				loader: 'file-loader',
				options: {
					name: '[name].[ext]',
					outputPath: 'fonts/'
				}
			}
		]
	},
	{
		test: /\.scss$/,
		exclude: /node_modules/,
		use: [
			MiniCssExtractPlugin.loader,
			'css-loader',
			'postcss-loader',
			'sass-loader'
		]
	},
	{ 
		test: /\.css$/,
		use: [ 
			'style-loader',
			'css-loader'
		]
	}
];

const optimization = [
	new OptimizeCssAssetsPlugin( {
		cssProcessor: cssnano
	} ),

	new TerserPlugin()
];

module.exports = ( argv ) => ( {
	entry: entry,
	output: output,
	devtool: 'source-map',
	plugins: plugins( argv ),

	module: {
		'rules': rules
	},

	optimization: {
		minimize: true,
		minimizer: optimization
	},

	externals: {
		jquery: 'jQuery'
	}

} );
