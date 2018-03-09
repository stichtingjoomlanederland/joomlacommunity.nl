'use strict';

// Generates a custom Modernizr build
module.exports = {
    dist: {
        cache: true,
        dest: '<%= paths.template %>/js/modernizr.js',
        options: ['html5shiv', 'prefixedCSS', 'setClasses'],
        uglify: true,
        tests: ['appearance',
            'csscalc', // For the grid
            'csstransforms',
            'checked',
            'flexbox',
            'flexboxlegacy',
            'flexwrap',
            'svg',
            'localstorage'
        ],
        crawl: false,
        customTests: []
    }
};
