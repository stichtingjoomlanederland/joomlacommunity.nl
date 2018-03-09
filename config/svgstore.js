'use strict';

//
module.exports = {
    default: {
        options: {
            svg: {
                viewBox : '0 0 100 100',
                xmlns: 'http://www.w3.org/2000/svg',
                'xmlns:xlink': 'http://www.w3.org/1999/xlink'
            },
            cleanup: true
        },
        files: {
            '<%= paths.template %>/icons/icons.svg': [
                '<%= paths.assets %>/icons/svg/*.svg'
            ]
        }
    }
};
