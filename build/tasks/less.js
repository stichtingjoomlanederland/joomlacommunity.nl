/* eslint-disable */

'use strict'

const less = require('../modules/less')

const src = process.env.npm_package_config_src + '/less'
const dest = process.env.npm_package_config_src + '/css'

module.exports = options => {

	const file = options.file

	less({
		src: `${src}/template.less`,
		dest: `${dest}/template.css`
	})
}

/* eslint-enable */
