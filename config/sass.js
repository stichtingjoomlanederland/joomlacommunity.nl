
'use strict';

//
module.exports = {
    dist: {
        options: {
            includePaths: [
                require("node-normalize-scss").includePaths
            ],
            style: 'expanded',
            sourceMap: false
        },
        files: {
            '<%= paths.assets %>/css/font.css': '<%= paths.assets %>/scss/font.scss',
            '<%= paths.assets %>/css/style.css': '<%= paths.assets %>/scss/style.scss'
        }
    }
};