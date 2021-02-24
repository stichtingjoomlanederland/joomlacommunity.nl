/* eslint-disable */

'use strict';

const sass = require('../modules/sass');

const src = process.env.npm_package_config_src + '/scss';
const dest = process.env.npm_package_config_src + '/css';
const styleguide = process.env.npm_package_config_styleguide;

module.exports = options => {

    const file = options.file;

    sass({
        src: `${src}/style.scss`,
        dest: `${dest}/style.css`
    });

    if (file === 'dev.scss' || file === '_styleguide-adjustments.scss') {
        sass({
            src: `${src}/dev.scss`,
            dest: `${dest}/dev.css`
        });
    }

    if (file === 'font.scss') {
        sass({
            src: `${src}/font.scss`,
            dest: `${dest}/font.css`
        });
    }

    if (file === 'styleguide.scss' || file === '_illusion-settings.scss') {
        sass({
            src: `${styleguide}/scss/styleguide.scss`,
            dest: `${styleguide}/css/styleguide.css`
        });
    }

};

/* eslint-enable */
