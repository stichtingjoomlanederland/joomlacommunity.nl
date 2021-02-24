/* eslint-disable */

'use strict';

/**
 * Kill process listening to given port
 * Command Line fallback method:
 * lsof -n -i:$npm_package_config_port | awk '$8 == \"TCP\" {print $2}' | xargs -r kill -9
 */

const lsof = require('lsof');
const ps = require('ps-node');

module.exports = options => {

    const port = options.port;

    lsof.rawTcpPort(port, data => {
        for (let key in data) {
            if (data.hasOwnProperty(key)) {
                let pid = data[key].pid
                    ,
                    state = data[key].state;

                if (state === 'listen') {
                    ps.kill(pid, err => {
                        if (err) {
                            throw err;
                        } else {
                            console.log(' Killed process with pid ' + pid);
                        }
                    });
                }
            }
        }
    });
};

/* eslint-enable */
