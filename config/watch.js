'use strict';

// Watches files for changes and runs tasks based on the changed files
module.exports = {
    gruntfile: {
        files: ['Gruntfile.js']
    },
    fontcustom: {
        files: ['<%= paths.assets %>/icons/svg/*.svg'],
        tasks: ['webfont', 'sass', 'autoprefixer'],
        options: {
            interrupt: true,
            atBegin: false
        }
    },
    sass: {
        files: [
            '<%= paths.assets %>/scss/*.scss',
            '<%= paths.assets %>/scss/**/*.scss'
        ],
        tasks: ['sass', 'autoprefixer', 'cssmin'],
        options: {
            interrupt: true,
            atBegin: true
        }
    },
    less: {
        files: [
            '<%= paths.assets %>/less/*.less',
            '<%= paths.assets %>/less/**/*.less'
        ],
        tasks: ['less', 'autoprefixer', 'cssmin'],
        options: {
            //livereload: true,
            interrupt: true,
            atBegin: true
        }
    },
    concat: {
        files: [
            '<%= paths.assets %>/scripts/*.js'
        ],
        tasks: ['concat'],
        options: {
            interrupt: true,
            atBegin: true
        }
    },
    svg: {
        files: [
            '<%= paths.assets %>/icons/svg/*.svg'
        ],
        tasks: ['svgstore'],
        options: {
            interrupt: true,
            atBegin: true
        }
    }
};
