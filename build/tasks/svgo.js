/* eslint-disable */

'use strict';

const svgo = require('../modules/svgo');
const copy = require('../modules/copy');
const src = process.env.npm_package_config_src;
const dest = process.env.npm_package_config_dist;

module.exports = options => {

	svgo({
		src: `${src}/icons/_sprite`,
		dest: `${dest}/icons`
	});

	copy({
		src: `${src}/icons/_save`,
		dest: `${dest}/icons`
	});

};

/* eslint-enable */
