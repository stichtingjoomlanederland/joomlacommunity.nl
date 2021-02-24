/* eslint-disable */

'use strict';

const copy = require('../modules/copy');
const symlink = require('../modules/symlink');
const clean = require('../tasks/clean');
const mkdirp = require('../tasks/mkdirp');
const less = require("../tasks/less");
const sass = require('../tasks/sass');
const postcss = require('../tasks/postcss');
const concat = require('../tasks/concat');
const babel = require('../tasks/babel');
const modernizr = require('../tasks/modernizr');
const svgstore = require('../tasks/svgstore');
const svgo = require('../tasks/svgo');

const currentPath = process.cwd();
const dest = process.env.npm_package_config_dist;

// prebuild
symlink({
	src: `../../hooks/pre-commit`,
	dest: `${currentPath}/.git/hooks/pre-commit`
});

// Configuration
copy({
	src: `configuration.php.dist`,
	dest: `public_html/configuration.php`
});

// htaccess
copy({
	src: `htaccess.local.txt`,
	dest: `public_html/.htaccess`
});

// Phpcs config
copy({
	src: `hooks/CodeSniffer.conf.dist`,
	dest: `hooks/CodeSniffer.conf`
});

// Live accessibilty checker
copy({
	src: `node_modules/@khanacademy/tota11y/dist/tota11y.min.js`,
	dest: `${dest}/js/tota11y.min.js`
});

// clean and create
clean();
mkdirp();

// css
less({file: 'template.less'});
less({file: 'easydiscuss.less'});
sass({file: 'style.scss'});
sass({file: 'dev.scss'});
sass({file: 'font.scss'});
setTimeout(() => {
	postcss({file: 'template.css'});
	postcss({file: 'easydiscuss.css'});
	postcss({file: 'style.css'});
	postcss({file: 'dev.css'});
	postcss({file: 'font.css'});
}, 2000);

// js
concat({file: 'main.js'});
setTimeout(() => {
	babel({file: 'scripts.concat.js'});
}, 2000);
copy({
  src: `node_modules/jquery/dist/jquery.min.js`,
  dest: `${dest}/js/jquery.min.js`
});

modernizr();

// icons
svgstore();
svgo();

/* eslint-enable */
