/* eslint-disable */

'use strict';

/**
 * Create the modernizr file
 */

const modernizr = require('modernizr');
const fs = require('fs');
const mkdirp = require('mkdirp');
const getDirName = require('path').dirname;
const opts = require('../../modernizr-config.json');

module.exports = options => {

    const dest = options.dest;

    mkdirp(getDirName(dest));

    modernizr.build(opts, result => {
        fs.writeFileSync(dest, result, (err) => {
            if (err) {
                throw err;
            }
        });
    });

    console.log(' Created ' + dest);

};

/* eslint-enable */
