/* eslint-disable */

'use strict';

const babel = require('../modules/babel');

const src = process.env.npm_package_config_src + '/js';
const dest = process.env.npm_package_config_dist + '/js';

module.exports = options => {

	const file = options.file;

	if (file === 'scripts.concat.js') {
		babel({
			src: `${src}/scripts.concat.js`,
			dest: `${dest}/scripts.js`
		});
	}

};

/* eslint-enable */
