/* eslint-disable */

'use strict';

const modernizr = require('../modules/modernizr');

const dest = process.env.npm_package_config_dist;

module.exports = options => {

    modernizr({
        dest: `${dest}/js/modernizr.js`
    });

};

/* eslint-enable */
