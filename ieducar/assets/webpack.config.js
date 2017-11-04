var webpack = require("webpack");

module.exports = {
	entry: './js/index.js',
	output: {
		path: __dirname + '/js',
		filename: 'bundle.js'
	},
	resolve : {
		modules: [
			'node_modules'
		],
		alias: {
			jquery: "jquery/dist/jquery.js",
		}
	},
	module : {
		loaders: [
			{
				test: /datatables\.net.*/,
				loader: 'imports-loader?define=>false'
			},
			{ //jQuery Plugins
				test: /vendor\/.+\.(jsx|js)$/,
				loader: 'imports-loader?jQuery=jquery,$=jquery,this=>window'
			},
			{ // CSS
				test: /\.css$/,
				loader: 'style-loader!css-loader'
			},
			{
				test: /node_modules\/jszip\/.*\.js$/,
				loader: 'imports-loader?JSZIP=jszip'
			}
		]
	},
	plugins:[

		//inject all the files above into this file
		new webpack.ProvidePlugin({
			jQuery: 'jquery',
			$: 'jquery',
			jquery: 'jquery'
		})

	]
}
