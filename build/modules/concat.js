/* eslint-disable */

'use strict';

/**
 * Concatenate an array of files
 */

const fs = require('fs');
const mkdirp = require('mkdirp');
const getDirName = require('path').dirname;

module.exports = options => {

    const files = options.src;
    const dest = options.dest;

    let out = files.map(filePath => {
        return fs.readFileSync(filePath).toString();
    });

    console.time(' Built ' + dest);

    mkdirp(getDirName(dest));

    fs.writeFile(dest, out.join('\n'), (err) => {
        if (err) throw err;
    });

    console.timeEnd(' Built ' + dest);

};

/* eslint-enable */
