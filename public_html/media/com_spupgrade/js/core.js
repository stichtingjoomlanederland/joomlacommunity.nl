/* 
 * Copyright (C) 2014 KAINOTOMO PH LTD <info@kainotomo.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (typeof (SPCYEND_core) === 'undefined') {
    var SPCYEND_core = {};
}
if (typeof (interval_var) === 'undefined') {
    var interval_var;
}

/**
 * Transfer selected items
 * 
 * @param {string} task
 * @param {string} form
 * @returns {Boolean}
 */
SPCYEND_core.transfer = function(task, form) {

    if (typeof (form) === 'undefined') {
        form = document.getElementById('adminForm');
    }

    if (typeof (task) !== 'undefined' && task !== "") {
        form.task.value = task;
    }

    //set all to transfer
    var status_arr = document.getElementsByName('status[]');
    var input_ids_arr = document.getElementsByName('input_ids[]');
    var cid_arr = document.getElementsByName('cid[]');
    var cid_length = cid_arr.length;
    for (k = 0; k < cid_length; k++)
    {
        if (k in input_ids_arr) {
            if (cid_arr[k].checked === true) {
                status_arr[k].value = 'pending';
            } else {
                status_arr[k].value = 'completed';
            }
        } else {
            cid_arr[k].checked = false;
        }
    }

    //hide table and toobar
    var spupgrade_table = document.getElementById('spupgrade_table');
    spupgrade_table.hidden = true;
    var toolbar = document.getElementById('toolbar');
    toolbar.hidden = true;

    //set message
    var message_div = document.getElementById('cyend_log');
    message_div.hidden = false;
    var message = '<p><img src="../media/com_spupgrade/images/processing.gif" alt="">'
            + ' processing - please wait...</p>';
    for (k = 0; k < cid_length; k++)
    {
        if (cid_arr[k].checked === true) {
            var name = document.getElementById('names' + k.toString());
            message += '<p><b>' + name.innerHTML + '</b></p>';
            break;
        }
    }
    message_div.innerHTML = message;

    interval_var = setInterval(function() {
        SPCYEND_core.get_last_id();
    }, 15000);

    //submit form
    AJAXSubmit(form);

    return true;

};

/**
 * Transfer all items
 * 
 * @param {string} task
 * @param {string} form
 * @returns {Boolean}
 */
SPCYEND_core.transfer_all = function(task, form) {

    if (typeof (form) === 'undefined') {
        form = document.getElementById('adminForm');
    }

    if (typeof (task) !== 'undefined' && task !== "") {
        form.task.value = task;
    }

    //set all to transfer
    var status_arr = document.getElementsByName('status[]');
    var input_ids_arr = document.getElementsByName('input_ids[]');
    var cid_arr = document.getElementsByName('cid[]');
    var cid_length = cid_arr.length;
    for (k = 0; k < cid_length; k++)
    {
        cid_arr[k].checked = false;
        if (k in input_ids_arr) {
            cid_arr[k].checked = true;
            status_arr[k].value = 'pending';
        }
    }

    //hide table and toobar
    var spupgrade_table = document.getElementById('spupgrade_table');
    spupgrade_table.hidden = true;
    var toolbar = document.getElementById('toolbar');
    toolbar.hidden = true;

    //set message
    var message_div = document.getElementById('cyend_log');
    message_div.hidden = false;
    var message = '<p><img src="../media/com_spupgrade/images/processing.gif" alt="">'
            + ' processing - please wait...</p>';
    for (k = 0; k < cid_length; k++)
    {
        if (cid_arr[k].checked === true) {
            var name = document.getElementById('names' + k.toString());
            message += '<p><b>' + name.innerHTML + '</b></p>';
            break;
        }
    }
    message_div.innerHTML = message;

    interval_var = setInterval(function() {
        SPCYEND_core.get_last_id();
    }, 15000);

    //submit form
    AJAXSubmit(form);

    return true;

};

/**
 * Transfer template
 * 
 * @param {string} task
 * @param {string} form
 * @returns {Boolean}
 */
SPCYEND_core.transfer_template = function(task, form) {

    if (typeof (form) === 'undefined') {
        form = document.getElementById('adminForm');
    }

    if (typeof (task) !== 'undefined' && task !== "") {
        form.task.value = task;
    }

    //check template name
    var template_name = document.getElementById('input_template');
    if (template_name.value === "") {
        alert('Please insert the template folder name and try again.');
        return;
    }

    //set all to transfer
    var cid_arr = document.getElementsByName('cid[]');
    var cid_length = cid_arr.length;
    for (k = 0; k < cid_length; k++)
    {
        cid_arr[k].checked = false;
        if (k === 17) {
            cid_arr[k].checked = true;
        }
    }

    //hide table and toobar
    var spupgrade_table = document.getElementById('spupgrade_table');
    spupgrade_table.hidden = true;
    var toolbar = document.getElementById('toolbar');
    toolbar.hidden = true;

    //set message
    var message_div = document.getElementById('cyend_log');
    message_div.hidden = false;
    var message = '<p><img src="../media/com_spupgrade/images/processing.gif" alt="">'
            + ' processing - please wait...</p>';
    for (k = 0; k < cid_length; k++)
    {
        if (cid_arr[k].checked === true) {
            var name = document.getElementById('names' + k.toString());
            message += '<p><b>' + name.innerHTML + '</b></p>';
            break;
        }
    }
    message_div.innerHTML = message;

    interval_var = setInterval(function() {
        SPCYEND_core.dummy();
    }, 15000);

    //submit form
    AJAXSubmit(form);

    return true;

};

/**
 * Transfer images
 * 
 * @param {string} task
 * @param {string} form
 * @returns {Boolean}
 */
SPCYEND_core.transfer_images = function(task, form) {

    if (typeof (form) === 'undefined') {
        form = document.getElementById('adminForm');
    }

    if (typeof (task) !== 'undefined' && task !== "") {
        form.task.value = task;
    }

    //set all to transfer
    var cid_arr = document.getElementsByName('cid[]');
    var cid_length = cid_arr.length;
    for (k = 0; k < cid_length; k++)
    {
        cid_arr[k].checked = false;
        if (k === 16) {
            cid_arr[k].checked = true;
        }
    }

    //hide table and toobar
    var spupgrade_table = document.getElementById('spupgrade_table');
    spupgrade_table.hidden = true;
    var toolbar = document.getElementById('toolbar');
    toolbar.hidden = true;

    //set message
    var message_div = document.getElementById('cyend_log');
    message_div.hidden = false;
    var message = '<p><img src="../media/com_spupgrade/images/processing.gif" alt="">'
            + ' processing - please wait...</p>';
    for (k = 0; k < cid_length; k++)
    {
        if (cid_arr[k].checked === true) {
            var name = document.getElementById('names' + k.toString());
            message += '<p><b>' + name.innerHTML + '</b></p>';
            break;
        }
    }
    message_div.innerHTML = message;

    interval_var = setInterval(function() {
        SPCYEND_core.dummy();
    }, 15000);

    //submit form
    AJAXSubmit(form);

    return true;

};

/**
 * Handle completion of each request
 * 
 * @param {string} responseText
 * @returns {Boolean|undefined}
 */
SPCYEND_core.completed = function(responseText) {

    if (typeof (form) === 'undefined') {
        form = document.getElementById('adminForm');
    }

    if (typeof (task) !== 'undefined' && task !== "") {
        form.task.value = task;
    }

    //get message
    var message_div = document.getElementById('cyend_log');

    //set status
    var cid_arr = document.getElementsByName('cid[]');
    var status_arr = document.getElementsByName('status[]');

    var cid_arr_length = cid_arr.length;
    var completed = true;
    for (k = 0; k < cid_arr_length; k++)
    {
        if ((cid_arr[k].checked === true) && (status_arr[k].value !== 'completed')) {
            completed = false;
        }
    }
    if (completed === true) {
        clearInterval(interval_var);
        var div_log = document.getElementById('get_last_id');
        div_log.hidden = true;
        message = '<p>Process Completed.</p>';
        message_div.innerHTML = message;
        return;
    }


    //decode responseText
    try {
        var result = eval("(" + responseText + ")");
    }
    catch (err)
    {
        message_div.innerHTML = responseText;
        clearInterval(interval_var);
        var div_log = document.getElementById('get_last_id');
        div_log.hidden = true;
        return;
    }

    //status    
    for (k = 0; k < cid_arr_length; k++)
    {
        if ((cid_arr[k].checked === true)) {
            status_arr[k].value = result.status;
            if (result.status === 'completed') {
                cid_arr[k].checked = false;
            }
            break;
        }
    }

    //write status in log
    var message = '<p><img src="../media/com_spupgrade/images/processing.gif" alt="">'
            + ' ' + SPCYEND_core.randomString() + '</p>';
    //message += '<p>Status: <b>' + result.status + '</b><br/></p>';    
    for (k = 0; k < cid_arr_length; k++)
    {
        if (cid_arr[k].checked === true) {
            var name = document.getElementById('names' + k.toString());
            message += '<p><b>' + name.innerHTML + '</b></p>';
            break;
        }
    }

    message_div.innerHTML = message;

    //submit form
    AJAXSubmit(form);

    return true;

};

/**
 * Create randomly two stings
 * 
 * @returns {String}
 */
SPCYEND_core.randomString = function() {
    var result = Math.floor((Math.random() * 2) + 1);
    if (result === 1) {
        return 'please wait...';
    } else {
        return 'processing...';
    }
};

/**
 * Get last id from history log
 * 
 * @returns {undefined}
 */
SPCYEND_core.get_last_id = function() {

    var url = 'index.php?option=com_spupgrade&task=log.get_last_id';
    var div_log = document.getElementById('get_last_id');
    div_log.hidden = false;

    var request = new XMLHttpRequest();
    request.open('GET', url, false);  // `false` makes the request synchronous
    try
    {
        request.send(null);
    }
    catch (err)
    {
        clearInterval(interval_var);
        var txt = "There was an error.\n\n";
        txt += "Error description: " + err.message + "\n\n";
        txt += "Click OK to continue.\n\n";
        alert(txt);
    }


    div_log.innerHTML = request.responseText;

};

/**
 * Dummy function that does nothing.
 * 
 * @returns {undefined}
 */
SPCYEND_core.dummy = function() {

    return;

};
