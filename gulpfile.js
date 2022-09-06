'use strict';

const {watch, series, parallel, src, dest} = require('gulp'),
	sass = require('gulp-sass')(require('sass')),
	autoprefixer = require('gulp-autoprefixer'),
	csso = require('gulp-csso'),
	webpack = require('webpack-stream');

const styles = () => {
	return src('./assets/scss/style.scss')
		.pipe(sass())
		.pipe(autoprefixer())
		.pipe(csso())
		.pipe(dest('./assets/css'));
};

const scripts = () => {
	return src([
		'./assets/js/scripts.js'
	])
		.pipe(webpack({
			output: {
				filename: 'scripts.min.js'
			},
			module: {
				rules: [
					{
						test   : /\.(js)$/,
						exclude: /(node_modules)/,
						loader : 'babel-loader',
						query  : {
							presets: [
								['@babel/preset-env', {
									targets: {
										esmodules: true,
									},
								}]
							]
						},
					}
				],
			},
			watch : false,
			mode  : 'production'
		}))
		.pipe(dest('./assets/js'));
};

const watcher = () => {
	watch('./assets/scss/**/*.scss', styles);
	watch(['./assets/js/scripts.js', './assets/js/components/*.js',], scripts);
};

exports.default = series(parallel(styles, scripts), watcher);