/* eslint-disable */

'use strict';

const svgstore = require('../modules/svgstore');

const src = process.env.npm_package_config_src;
const dest = process.env.npm_package_config_dist;

module.exports = options => {

    svgstore({
        src: `${src}/icons/_sprite`,
        dest: `${dest}/icons/sprite.svg`
    });

};

/* eslint-enable */
