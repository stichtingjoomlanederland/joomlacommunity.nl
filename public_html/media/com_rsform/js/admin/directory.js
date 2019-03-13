jQuery.formTabs = {
    tabTitles: {},
    tabContents: {},

    build: function (startindex) {
        this.each(function (index, el) {
            var tid = jQuery(el).attr('id');
            jQuery.formTabs.grabElements(el,tid);
            jQuery.formTabs.makeTitlesClickable(tid);
            jQuery.formTabs.setAllContentsInactive(tid);
            jQuery.formTabs.setTitleActive(startindex,tid);
            jQuery.formTabs.setContentActive(startindex,tid);
        });
    },

    grabElements: function(el,tid) {
        var children = jQuery(el).children();
        children.each(function(index, child) {
            if (index == 0)
                jQuery.formTabs.tabTitles[tid] = jQuery(child).find('a');
            else if (index == 1)
                jQuery.formTabs.tabContents[tid] = jQuery(child).children();
        });
    },

    setAllTitlesInactive: function (tid) {
        this.tabTitles[tid].each(function(index, title) {
            jQuery(title).removeClass('active');
        });
    },

    setTitleActive: function (index,tid) {
        index = parseInt(index);
        if (tid == 'rsform_tab3') document.getElementById('ptab').value = index;
        jQuery(this.tabTitles[tid][index]).addClass('active');
    },

    setAllContentsInactive: function (tid) {
        this.tabContents[tid].each(function(index, content) {
            jQuery(content).hide();
        });
    },

    setContentActive: function (index,tid) {
        index = parseInt(index);
        jQuery(this.tabContents[tid][index]).show();
    },

    makeTitlesClickable: function (tid) {
        this.tabTitles[tid].each(function(index, title) {
            jQuery(title).click(function () {
                jQuery.formTabs.setAllTitlesInactive(tid);
                jQuery.formTabs.setTitleActive(index,tid);

                jQuery.formTabs.setAllContentsInactive(tid);
                jQuery.formTabs.setContentActive(index,tid);
            });
        });
    }
};

jQuery.fn.extend({
    formTabs: jQuery.formTabs.build
});


function toggleOrderSpansDir() {
    var table = jQuery('#dirSubmissionsTable tbody tr');
    var k = 0;

    for (i=0; i<table.length; i++) {
        jQuery(table[i]).removeClass('row0');
        jQuery(table[i]).removeClass('row1');
        jQuery(table[i]).addClass('row' + k);
        k = 1 - k;
    }
}

function tidyOrderDir() {
    stateLoading();

    var params = [];
    var orders = document.getElementsByName('dirorder[]');
    var cids = document.getElementsByName('dircid[]');
    var formId = document.getElementById('formId').value;

    for (i=0; i<orders.length; i++) {
        params.push('cid[' + cids[i].value + ']=' + parseInt(i + 1));
        orders[i].value = i + 1;
    }

    params.push('formId='+formId);

    toggleOrderSpansDir();

    var xml = buildXmlHttp();

    var url = 'index.php?option=com_rsform&task=directory.save.ordering&randomTime=' + Math.random();
    xml.open("POST", url, true);

    params = params.join('&');

    //Send the proper header information along with the request
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xml.send(params);
    xml.onreadystatechange=function()
    {
        if(xml.readyState==4)
        {
            var autogenerate = document.getElementsByName('jform[ViewLayoutAutogenerate]');
            for (var i=0;i<autogenerate.length;i++)
                if (autogenerate[i].value == 1 && autogenerate[i].checked)
                    generateDirectoryLayout(formId, 'no');

            stateDone();
        }
    }
}

function dirAutoGenerate() {
    stateLoading();

    var params = [];
    var cids = document.getElementsByName('dirindetails[]');
    var formId = document.getElementById('formId').value;

    for (i=0; i<cids.length; i++) {
        if (cids[i].checked)
            params.push('cid[' + cids[i].value + ']=1');
        else
            params.push('cid[' + cids[i].value + ']=0');
    }

    params.push('formId='+formId);

    var xml = buildXmlHttp();

    var url = 'index.php?option=com_rsform&task=directory.save.details&randomTime=' + Math.random();
    xml.open("POST", url, true);

    params = params.join('&');

    //Send the proper header information along with the request
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xml.send(params);
    xml.onreadystatechange=function()
    {
        if(xml.readyState==4)
        {
            var autogenerate = document.getElementsByName('jform[ViewLayoutAutogenerate]');
            for (var i=0;i<autogenerate.length;i++)
                if (autogenerate[i].value == 1 && autogenerate[i].checked)
                    generateDirectoryLayout(formId, 'no');

            stateDone();
        }
    }
}

function dirSelectAll(what) {
    var $elements = jQuery(document.getElementsByName(what + '[]'));
    var $checkbox = jQuery(document.getElementById(what + 'check'));
    $elements.prop('checked', $checkbox.prop('checked'));
}

function toggleQuickAddDirectory() {
    var what = 'none';
    if (document.getElementById('QuickAdd1').style.display == 'none')
        what = '';
    document.getElementById('QuickAdd1').style.display = what;
}