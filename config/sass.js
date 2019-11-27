'use strict';

const sass = require('node-sass');

//
module.exports = {
    dist: {
        options: {
            style: 'expanded',
            sourceMap: false,
            implementation: sass
        },
        files: {
            '<%= paths.assets %>/css/font.css': '<%= paths.assets %>/scss/font.scss',
            '<%= paths.assets %>/css/style.css': '<%= paths.assets %>/scss/style.scss'
        }
    }
};
