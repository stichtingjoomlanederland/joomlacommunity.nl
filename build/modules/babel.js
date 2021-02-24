/* eslint-disable */

'use strict';

/**
 * Used to babel Javascript files
 */

const fs = require('fs');
const babel = require('@babel/core');
const mkdirp = require('mkdirp');
const getDirName = require('path').dirname;

module.exports = ({src, dest}) => {
    console.time(' Built ' + dest);

    babel.transformFile(src, {}, (err, result) => {
        if (!err) {
            mkdirp(getDirName(dest));

            fs.writeFile(dest, result.code, (err) => {
                if (err) {
                    throw err;
                }
            });
        } else {
            throw err
        }
    });

    console.timeEnd(' Built ' + dest);
};

/* eslint-enable */
