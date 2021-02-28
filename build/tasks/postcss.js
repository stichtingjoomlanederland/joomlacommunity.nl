/* eslint-disable */

'use strict';

const postcss = require('../modules/postcss');

const src = process.env.npm_package_config_src + '/css';
const dest = process.env.npm_package_config_dist + '/css';

module.exports = options => {

    const file = options.file;

    if (file !== 'font.css') {
        postcss({
            src: `${src}/${file}`,
            dest: `${dest}/${file}`
        });
    }

    if (file === 'font.css') {
        postcss({
            src: `${src}/font.css`,
            dest: `${dest}/font.css`,
            opts: {
                level: {
                    2: {
                        restructureRules: true,
                        mergeSemantically: true
                    }
                }
            }
        });
    }

};

/* eslint-enable */
