'use strict';

module.exports = function(grunt) {

    // measures the time each task takes
    require('time-grunt')(grunt);

    // load time-grunt and all grunt plugins found in the package.json
    require('jit-grunt')(grunt, {
        versioncheck: 'grunt-version-check'
    });

    var options = {
        // Project settings
        paths: {
            // Configurable paths
            template: 'public_html/templates/jc',
            assets: 'public_html/templates/jc/assets'
        },
        browsersync : {
            port : '5666', //JOOM
            proxy: 'joomlacommunity.test',
            open: false
        }
    };

    // Load grunt configurations automatically
    var configs = require('load-grunt-configs')(grunt, options);

    // Define the configuration for all the tasks
    grunt.initConfig(configs);

    // The dev task will be used during development
    grunt.registerTask('default', [
        'shell',
        'copy',
        'modernizr',
        'browserSync',
        'watch'
    ]);

    // The js task will be used during development
    grunt.registerTask('deploy', [
        'shell',
        'copy',
        'modernizr',
        'browserSync',
        'webfont',
        'less', 'autoprefixer', 'cssmin',
        'uglify'
    ]);

};
