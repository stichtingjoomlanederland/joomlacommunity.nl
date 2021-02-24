/* eslint-disable */

'use strict';


/**
 * combine svg files to svg sprite
 */

const clean = require('../modules/clean');

const svgstore = require('svgstore');
const fs = require('fs');
const path = require('path');

module.exports = options => {

    const src = options.src;
    const dest = options.dest;

    // remove old sprite
    clean({
        dir: dest
    });

    // create new sprite
    let sprites = svgstore({
        svgAttrs: {
            style: 'display: none'
        }
    });

    fs.readdirSync(src)
        .forEach(file => {
            sprites.add(file.replace(/\.svg/, ''), fs.readFileSync(path.join(src, file)));
        });

    fs.writeFileSync(dest, sprites.toString({inline: true}));

    console.log(' Created ' + dest);
};

/* eslint-enable */
