/* eslint-disable */

'use strict'

/**
 * Compile LESS files
 */

const less = require('less')
const fs = require('fs')
const mkdirp = require('mkdirp')
const getDirName = require('path').dirname

module.exports = options => {

	const src = options.src
	const dest = options.dest

	setTimeout(() => {

		console.time(' Built ' + dest)

		fs.readFile(src, (error, data) => {
			data = data.toString()

			less.render(data, {
				paths: [getDirName(src)]
			}, (e, css) => {
				fs.writeFile(dest, css.css, (err) => {
					if (err) {
						console.log('\x1b[31m', error, '\x1b[0m')
					}
				})
			})
		})

		console.timeEnd(' Built ' + dest)
	}, 50)

}

/* eslint-enable */
