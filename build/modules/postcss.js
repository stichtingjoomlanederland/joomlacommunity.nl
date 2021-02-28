/* eslint-disable */

'use strict';

/**
 * Run autoprefixer on a CSS file
 */

const fs = require('fs');
const CleanCSS = require('clean-css');
const Autoprefixer = require('autoprefixer');
const postcss = require('postcss');

module.exports = (options = {}) => {

	const src = options.src;
	const dest = options.dest;

	let opts = options.opts || {
		level: {
			1: {
				specialComments: false
			},
			2: {
				mergeSemantically: true,
				restructureRules: true
			}
		}
	};

	let css = fs.readFileSync(src, 'utf8');

	console.time(' Optimized ' + dest);

	postcss([Autoprefixer])
		.process(css, {
			from: src,
			to: dest
		})
		.then(result => {
			result.warnings()
				.forEach(warn => {
					console.warn(warn.toString());
				});

			let output = new CleanCSS(opts).minify(result.css).styles;

			fs.writeFileSync(dest, output, () => true);
		});

	console.timeEnd(' Optimized ' + dest);

};

module.exports.postcss = true

/* eslint-enable */
