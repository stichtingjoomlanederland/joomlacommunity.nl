'use strict';

//
module.exports = {
    options: {
        level: 2,
        roundingPrecision: -1,
        sourceMap: true
    },
    dist: {
        files: [{
            expand: true,
            cwd: '<%= paths.assets %>/css',
            src: ['*.css', '!*.min.css'],
            dest: '<%= paths.template %>/css',
            ext: '.css'
        }]
    }
};
