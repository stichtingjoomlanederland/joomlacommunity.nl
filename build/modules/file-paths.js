/* eslint-disable */

'use strict';

/**
 * Get an array of files from a directory
 */

const path = path || require('path');
const fs = fs || require('fs');

let files = fs.readdirSync(dir);

// ignore these files
const blacklist = [
    '.DS_Store'
];

module.exports = (dir, filelist) => {

    filelist = filelist || [];

    files.forEach(file => {
        if (blacklist.indexOf(file) > -1) {
            console.log('file list contains suspicious file(s) which have been excluded: ' + file);
        } else {
            if (fs.statSync(path.join(dir, file))
                .isDirectory()) {
                filelist = walkSync(path.join(dir, file), filelist);
            } else {
                filelist.push(dir + file);
            }
        }
    });

    return filelist;
};

/* eslint-enable */
