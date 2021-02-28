/* eslint-disable */

'use strict';

/**
 * Start a server using BrowserSync
 */

const bs = require('browser-sync').create();

module.exports = (options, done) => {

    bs.init(options, done);

    // When process exits kill browser-sync server
    process.on('exit', () => {
        bs.exit();
    });

};

/* eslint-enable */
