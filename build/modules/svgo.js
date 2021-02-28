/* eslint-disable */

'use strict'

/**
 * optimizing SVG vector graphics files
 */
const fs = require('fs')
const svgo = require('svgo')

const config = {
	plugins: [
		'cleanupAttrs',
		'removeDoctype',
		'removeXMLProcInst',
		'removeComments',
		'removeMetadata',
		'removeTitle',
		'removeDesc',
		'removeUselessDefs',
		'removeEditorsNSData',
		'removeEmptyAttrs',
		'removeHiddenElems',
		'removeEmptyText',
		'removeEmptyContainers',
		//'removeViewBox',
		'cleanupEnableBackground',
		'convertStyleToAttrs',
		'convertColors',
		'convertPathData',
		'convertTransform',
		'removeUnknownsAndDefaults',
		'removeNonInheritableGroupAttrs',
		'removeUselessStrokeAndFill',
		'removeUnusedNS',
		'cleanupIDs',
		'cleanupNumericValues',
		'moveElemsAttrsToGroup',
		'moveGroupAttrsToElems',
		'collapseGroups',
		//'removeRasterImages',
		'mergePaths',
		'convertShapeToPath',
		'sortAttrs',
		'removeDimensions',
		{ name: 'removeAttrs', attrs: '(stroke|fill|fill-rule|stroke-linejoin|stroke-miterlimit|clip-rule)' },
	]
}

module.exports = options => {

	const src = options.src
	const file = options.file
	const dest = options.dest
	const type = options.type || 'dir'

	if (type === 'file') {
		if (fs.statSync(src + '/' + file)
			.isFile()) {
			fs.readFile(src + '/' + file, 'utf8', (err, data) => {

				if (err) {
					throw err
				}

				const result = svgo.optimize(data, { path: dest, ...config })

				fs.writeFileSync(dest + '/' + file, result.data, () => true)

				console.log(' Optimized ' + file + ' to ' + dest + '/' + file)
			})
		}
	}

	if (type === 'dir') {
		fs.readdirSync(src)
			.forEach(file => {
				if (fs.statSync(src + '/' + file)
					.isFile()) {
					fs.readFile(src + '/' + file, 'utf8', (err, data) => {

						if (err) {
							throw err
						}

						const result = svgo.optimize(data, { path: dest, ...config })

						fs.writeFileSync(dest + '/' + file, result.data, () => true)

						console.log(' Optimized ' + file + ' to ' + dest + '/' + file)
					})
				}
			})
	}
}

/* eslint-enable */
