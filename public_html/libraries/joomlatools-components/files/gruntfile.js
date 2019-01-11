module.exports = function(grunt) {

    // measures the time each task takes
    require('time-grunt')(grunt);

    // load time-grunt and all grunt plugins found in the package.json
    require('jit-grunt')(grunt);

    var sass = require('node-sass');

    var jsFiles = [
        '<%= assetsPath %>/js/history/history.js',
        '<%= assetsPath %>/js/files.utilities.js',
        '<%= assetsPath %>/js/files.state.js',
        '<%= assetsPath %>/js/files.template.js',
        '<%= assetsPath %>/js/files.grid.js',
        '<%= assetsPath %>/js/files.tree.js',
        '<%= assetsPath %>/js/files.row.js',
        '<%= assetsPath %>/js/files.paginator.js',
        '<%= assetsPath %>/js/files.pathway.js',
        '<%= assetsPath %>/js/files.app.js',
        '<%= assetsPath %>/js/files.attachments.app.js',
        '<%= assetsPath %>/js/files.uploader.js',
        '<%= assetsPath %>/js/files.copymove.js'
    ];

    // grunt config
    grunt.initConfig({

        // Grunt variables
        assetsPath: 'resources/assets',

        // Compile sass files
        sass: {
            options: {
                implementation: sass,
                outputStyle: 'compact'
            },
            dist: {
                files: {
                    '<%= assetsPath %>/css/files.css': '<%= assetsPath %>/scss/files.scss',
                    '<%= assetsPath %>/css/uploader.css': '<%= assetsPath %>/scss/uploader.scss'
                }
            }
        },

        // Concatenate files
        concat: {
            js: {
                files: {
                    '<%= assetsPath %>/js/build/files.js': jsFiles
                }
            }
        },

        uglify: {
            options: {
                sourceMap: true,
                preserveComments: /(?:^!|@(?:license|preserve|cc_on))/ // preserve @license tags
            },
            build: {
                files: {
                    '<%= assetsPath %>/js/min/files.js': jsFiles
                }
            }
        },


        // Autoprefixer
        autoprefixer: {
            options: {
                browsers: ['> 5%', 'last 2 versions', 'ie 11']
            },
            files: {
                expand: true,
                flatten: true,
                src: '<%= assetsPath %>/css/*.css',
                dest: '<%= assetsPath %>/css/'
            }
        },


        // Watch files
        watch: {
            sass: {
                files: [
                    '<%= assetsPath %>/scss/*.scss',
                    '<%= assetsPath %>/scss/**/*.scss'
                ],
                tasks: ['sass', 'autoprefixer'],
                options: {
                    interrupt: true,
                    atBegin: true
                }
            }
            ,javascript: {
               files: [
                   '<%= assetsPath %>/js/*.js'
               ],
               tasks: ['uglify', 'concat'],
               options: {
                   interrupt: true,
                   atBegin: true
               }
            }
        }
    });

    // The dev task will be used during development
    grunt.registerTask('default', ['watch']);

};