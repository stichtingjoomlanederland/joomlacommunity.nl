'use strict';

// Browsersync
module.exports = {
    dev: {
        bsFiles: {
            src: ['<%= paths.template %>/css/*.*',
                '<%= paths.template %>/js/*.*',
                '<%= paths.template %>/img/*.*',
                '<%= paths.template %>/**/*.php',
                '<%= paths.template %>/index.php'
            ]
        },
        options: {
            proxy: '<%= browsersync.proxy %>',
            port: '<%= browsersync.port %>',
            open: true,
            notify: false,
            watchTask: true,
            injectChanges: false
        }
    }
};
