/* eslint-disable */

'use strict';

const mkdirp = require('../modules/mkdirp');

const dest = process.env.npm_package_config_dist;

module.exports = () => {

    mkdirp(`${dest}/css`);

    mkdirp(`${dest}/icons`);

    mkdirp(`${dest}/js`);

};

/* eslint-enable */
