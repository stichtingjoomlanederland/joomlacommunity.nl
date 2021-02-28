/* eslint-disable */

'use strict';

const concat = require('../modules/concat');

const src = process.env.npm_package_config_src + '/scripts';
const dest = process.env.npm_package_config_src + '/js';

module.exports = options => {

  const file = options.file;

  if (file === 'main.js' || file === 'flying-focus.js') {
    concat({
      src: [
        `${src}/flying-focus.js`,
        `${src}/main.js`
      ],
      dest: `${dest}/scripts.js`
    });
  }

};

/* eslint-enable */
