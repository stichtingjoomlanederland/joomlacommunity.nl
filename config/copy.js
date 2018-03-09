'use strict';

//
module.exports = {
    options: {
        separator: '\r\n'
    },

    // Default
    default: {
        files: [
            {
                expand: true,
                src: [
                    'node_modules/optimised-svgs/icons/misc/icon.svg'
                ],
                dest: '<%= paths.template %>/icons',
                flatten: true
            }
        ]
    }
};
