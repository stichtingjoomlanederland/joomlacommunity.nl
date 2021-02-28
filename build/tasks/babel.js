/* eslint-disable */

'use strict'

const babel = require('../modules/babel')

const src = process.env.npm_package_config_src + '/js'
const dest = process.env.npm_package_config_dist + '/js'

module.exports = options => {

	const file = options.file

	babel({
		src: `${src}/${file}`,
		dest: `${dest}/${file}`
	});
}

/* eslint-enable */
