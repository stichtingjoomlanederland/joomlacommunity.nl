if (typeof RSFormProDirectory != 'object') {
    var RSFormProDirectory = {};
}

RSFormProDirectory.clearUpload = function(name, button) {
    var parent = button.parentNode;

    parent.removeChild(button);
    parent.removeChild(parent.firstChild);

    var input = document.createElement('input');
    input.name = 'form[' + name + ']';
    input.type = 'hidden';

    parent.appendChild(input);
};

RSFormProDirectory.submit = function(task) {
    var form = document.adminForm;

    if (typeof task != 'undefined') {
        form.task.value = task;
    } else {
        form.task.value = '';
    }
    form.submit();
};

RSFormProDirectory.reset = function() {
    var form = document.adminForm;
    form.filter_search.value = '';

    RSFormProDirectory.submit();
};

RSFormProDirectory.downloadCSV = function() {
    var selected = false;
    var cids = document.getElementsByName('cid[]');
    for (var i=0; i<cids.length; i++) {
        if (cids[i].checked) {
            selected = true;
            break;
        }
    }

    if (!selected) {
        alert(Joomla.JText._('RSFP_SUBM_DIR_PLEASE_SELECT_AT_LEAST_ONE'));
        return;
    }

    RSFormProDirectory.submit('download');
};

// Legacy
var directorySubmit = RSFormProDirectory.submit;
var directoryReset = RSFormProDirectory.reset;
var directoryDownloadCSV = RSFormProDirectory.downloadCSV;