/* eslint-disable */

'use strict';

/**
 * Clean a directory
 */

const fse = require('fs-extra');

module.exports = options => {

    const dir = options.dir;

    fse.removeSync(dir);

    console.log(' Removed ' + dir);

};

/* eslint-enable */
