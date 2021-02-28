/* eslint-disable */

'use strict';

/**
 * Copy a file/directory from one location to another
 */

const fse = require('fs-extra');

module.exports = options => {

    const src = options.src;
    const dest = options.dest;


    fse.copy(src, dest, err => {
        if (err) {
            return console.error(err);
        }

        console.log(' Copied ' + src + ' to ' + dest);
    });

};

/* eslint-enable */
