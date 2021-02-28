/* eslint-disable */

'use strict';

/**
 * Compile Sass/Scss files
 */

const sass = require('node-sass');
const fs = require('fs');
const mkdirp = require('mkdirp');
const getDirName = require('path').dirname;

module.exports = options => {

    const src = options.src;
    const dest = options.dest;

    let style = options.style || 'expanded';

    console.time(' Built ' + dest);

    setTimeout(() => {
        let result;

        try {
            result = sass.renderSync({
                file: src,
                outputStyle: style
            });
        } catch (e) {
            console.log('\x1b[31m', e, '\x1b[0m');
            return;
        }

        if (result) {
            mkdirp(getDirName(dest));

            fs.writeFile(dest, result.css, (err) => {
                if (err) {
                    throw err;
                }
            });
        }

        console.timeEnd(' Built ' + dest);
    }, 50);

};

/* eslint-enable */
