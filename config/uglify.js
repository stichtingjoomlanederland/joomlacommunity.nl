'use strict';

//
module.exports = {
    options: {
        sourceMap: true
    },
    build: {
        files: {
            '<%= paths.template %>/js/scripts.js': '<%= templateScripts %>'
        }
    }
};
