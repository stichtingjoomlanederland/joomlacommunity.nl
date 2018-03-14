'use strict';

//
module.exports = {
    options: {
        separator: '\r\n'
    },
    default: {
        src: '<%= templateScripts %>',
        dest: '<%= paths.template %>/js/scripts.js'
    }
};
