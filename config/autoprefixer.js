'use strict';

// Add vendor prefixed styles
module.exports = {
    options: {
        browsers: ['> 5%', 'last 2 versions', 'ie 11', 'ie 10']
    },
    files: {
        expand: true,
        flatten: true,
        src: '<%= paths.assets %>/css/*.css',
        dest: '<%= paths.assets %>/css/'
    }
};
