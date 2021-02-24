/* eslint-disable */

'use strict';

/**
 * Create a symlink
 */

const fs = require('fs');

module.exports = options => {

    const src = options.src;
    const dest = options.dest;

    fs.symlinkSync(src, dest + '-tmp', err => {
        if (err) {
            return console.error(err);
        }

        console.log(' Symlink tmp created ' + src + ' to ' + dest + '-tmp');
    });

    fs.rename(dest + '-tmp', dest, err => {
        if (err) {
            return console.error(err);
        }

        console.log(' Symlink created ' + src + ' to ' + dest);
    });

};

/* eslint-enable */
