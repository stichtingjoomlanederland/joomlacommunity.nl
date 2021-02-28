/* eslint-disable */

'use strict';

require('dotenv').config();

// Modules
const serve = require('../modules/browser-sync');
const svgo = require('../modules/svgo');
const lsof = require('../modules/lsof');
const copy = require('../modules/copy');

// Tasks
const concat = require('../tasks/concat');
const babel = require('../tasks/babel');
const postcss = require('../tasks/postcss');
const less = require('../tasks/less');
const sass = require('../tasks/sass');
const svgostore = require('../tasks/svgstore');

// Other var
const bs = require('browser-sync').create();
const path = require('path');
const port = process.env.npm_package_config_port;
const src = process.env.npm_package_config_src;
const dest = process.env.npm_package_config_dist;
const proxy = process.env.PROXY || process.env.npm_package_config_proxy;
const styleguide = process.env.npm_package_config_styleguide;

let debug = false;

// Clear the port
lsof({
	port: port
});

// Start serving
serve({
	notify: true,
	open: true,
	watchEvents: ['change', 'add'],
	proxy: proxy,
	port: process.env.npm_package_config_port,
	files: [
		`${dest}/css/*.css`,
		`${dest}/icons/*.svg`,
		`${dest}/js/*.js`,
		`${dest}/images/*.*`,
		`${dest}/images/favicon/*.*`,
		`${dest}/**/*.php`,
		`${dest}/*.php`,
		`${styleguide}/css`,
	]
});

/**
 * Debounce
 * https://gist.github.com/nmsdvid/8807205
 * Returns a function, that, as long as it continues to be invoked, will not
 * be triggered. The function will be called after it stops being called for
 * N milliseconds. If `immediate` is passed, trigger the function on the
 * leading edge, instead of the trailing.
 *
 *
 * @param { function } callback  The callback function to be executed
 * @param { integer }  time      The time to wait before firing the callback
 * @param { integer }  interval  The interval
 */
const debounce = (callback, time = 250, interval) => (...args) => clearTimeout(interval, interval = setTimeout(callback, time, ...args));

const regexIcon = RegExp('icons[\\/][_]*[\\/]*\.svg');
const regexSprite = RegExp('icons[\\/]_sprite\/.*\.svg');
const regexScript = RegExp('scripts[\\/].*\.js');
const regexJs = RegExp('js[\\/].*\.js');
const regexLess = RegExp('less[\\/].*\.less');
const regexScss = RegExp('scss[\\/].*\.scss');
const regexCss = RegExp('[\\/]css[\\/].*\.css');

bs.watch(`${src}/**`, (event, file) => {

	if (event === 'add' || event === 'change') {

		// Icon files changes
		if (regexIcon.test(file)) {
			if (debug) {
				console.log('icons: ' + event + ' - ' + file);
			}

			copy({
				src: `${src}/icons/_save/${path.basename(file)}`,
				dest: `${dest}/icons/${path.basename(file)}`
			});
		}

		// Icon Sprite files changes
		if (regexSprite.test(file)) {
			if (debug) {
				console.log('icons: ' + event + ' - ' + file);
			}

			svgo({
				src: `${src}/icons/_sprite`,
				file: path.basename(file),
				dest: `${dest}/icons`,
				type: 'file'
			});
			svgostore();
		}

		// Css files changes
		if (regexCss.test(file)) {
			if (debug) {
				console.log('css: ' + event + ' - ' + file);
			}

			debounce(
				postcss({
					file: path.basename(file)
				})
				, 300);
		}

		// Script files changes
		if (regexScript.test(file)) {
			if (debug) {
				console.log('js: ' + event + ' - ' + file);
			}

			concat({
				file: path.basename(file)
			});
		}

		// JS files changes
		if (regexJs.test(file)) {
			if (debug) {
				console.log('js: ' + event + ' - ' + file);
			}

			babel({
				file: path.basename(file)
			});
		}
	}

	if (event === 'change') {
		// LESS files changes
		if (regexLess.test(file)) {
			if (debug) {
				console.log('less: ' + event + ' - ' + file);
			}

			debounce(
				less({
					file: path.basename(file)
				})
				, 300);
		}

		// Sass files changes
		if (regexScss.test(file)) {
			if (debug) {
				console.log('sass: ' + event + ' - ' + file);
			}

			debounce(
				sass({
					file: path.basename(file)
				})
				, 300);
		}
	}

	if (event === 'unlink') {
		// Icon Sprite files changes
		if (regexSprite.test(file)) {
			svgostore();
		}
	}
});

// Styleguide
bs.watch(`${styleguide}/**`, (event, file) => {

	if (event === 'change') {
		// Sass files changes
		if (regexScss.test(file)) {
			if (debug) {
				console.log('sass: ' + event + ' - ' + file);
			}

			debounce(
				sass({
					file: path.basename(file)
				})
				, 300);
		}
	}
});

/* eslint-enable */
