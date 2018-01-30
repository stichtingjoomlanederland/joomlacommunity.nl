<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive'); ?>

<form action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	
	<div class="alert alert-error" id="rsform_layout_msg" <?php if ($this->directory->ViewLayoutAutogenerate) { ?>style="display: none"<?php } ?>>
		<?php echo JText::_('RSFP_SUBM_DIR_AUTOGENERATE_LAYOUT_DISABLED'); ?>
	</div>
	<br />
	
	<div id="rsform_container">
		<div id="state" style="display: none;"><img src="components/com_rsform/assets/images/load.gif" alt="<?php echo JText::_('RSFP_PROCESSING'); ?>" /><?php echo JText::_('RSFP_PROCESSING'); ?></div>
		
		<div id="rsform_tab3">
			<ul class="rsform_leftnav" id="rsform_secondleftnav">
				<li class="rsform_navtitle"><?php echo JText::_('RSFP_DIRECTORY_TAB'); ?></li>
				<li><a href="javascript: void(0);" id="editform"><span class="rsficon rsficon-pencil-square"></span><span class="inner-text"><?php echo JText::_('RSFP_DIRECTORY_EDIT'); ?></span></a></li>
                <li><a href="javascript: void(0);" id="permissions"><span class="rsficon rsficon-shield"></span><span class="inner-text"><?php echo JText::_('RSFP_DIRECTORY_PERMISSIONS'); ?></span></a></li>
				<li><a href="javascript: void(0);" id="fields"><span class="rsficon rsficon-list-alt"></span><span class="inner-text"><?php echo JText::_('RSFP_DIRECTORY_FIELDS'); ?></span></a></li>
				<li class="rsform_navtitle"><?php echo JText::_('RSFP_DESIGN_TAB'); ?></li>
				<li><a href="javascript: void(0);" id="formlayout"><span class="rsficon rsficon-th-list"></span><span class="inner-text"><?php echo JText::_('RSFP_SUBM_DIR_DETAILS_LAYOUT'); ?></span></a></li>
				<li><a href="javascript: void(0);" id="cssandjavascript"><span class="rsficon rsficon-file-code-o"></span><span class="inner-text"><?php echo JText::_('RSFP_CSS_JS'); ?></span></a></li>
				<li class="rsform_navtitle"><?php echo JText::_('RSFP_EMAILS_TAB'); ?></li>
				<li><a href="javascript: void(0);" id="emails"><span class="rsficon rsficon-envelope-o"></span><span class="inner-text"><?php echo JText::_('RSFP_SUBM_DIR_EMAILS'); ?></span></a></li>
				<li class="rsform_navtitle"><?php echo JText::_('RSFP_SCRIPTS_TAB'); ?></li>
				<li><a href="javascript: void(0);" id="scripts"><span class="rsficon rsficon-code"></span><span class="inner-text"><?php echo JText::_('RSFP_FORM_SCRIPTS'); ?></span></a></li>
				<li><a href="javascript: void(0);" id="emailscripts"><span class="rsficon rsficon-file-code-o"></span><span class="inner-text"><?php echo JText::_('RSFP_EMAIL_SCRIPTS'); ?></span></a></li>
			</ul>
			
			<div id="propertiescontent">
				<div id="editformdiv">
					<p><?php echo $this->loadTemplate('general'); ?></p>
				</div>
                <div id="permissionsdiv">
                    <p><?php echo $this->loadTemplate('permissions'); ?></p>
                </div>
				<div id="fieldsdiv">
					<p><?php echo $this->loadTemplate('fields'); ?></p>
				</div>
				<div id="formlayoutdiv">
					<p><?php echo $this->loadTemplate('layout'); ?></p>
				</div>
				<div id="cssandjavascriptdiv">
					<p><?php echo $this->loadTemplate('cssjs'); ?></p>
				</div>
				<div id="emailsdiv">
					<p><?php echo $this->loadTemplate('emails'); ?></p>
				</div>
				<div id="scriptsdiv">
					<p><?php echo $this->loadTemplate('scripts'); ?></p>
				</div>
				<div id="emailscriptsdiv">
					<p><?php echo $this->loadTemplate('emailscripts'); ?></p>
				</div>
			</div>
			
		</div>
	</div>
	
	<input type="hidden" name="option" value="com_rsform">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="tab" id="ptab" value="0" />
	<input type="hidden" name="jform[formId]" id="formId" value="<?php echo $this->formId; ?>">
</form>

<script type="text/javascript">
RSFormPro.$(document).ready(function(){
	RSFormPro.$('#rsform_tab3').formTabs(<?php echo $this->tab; ?>);
	RSFormPro.$('#dirSubmissionsTable tbody').tableDnD({
		onDragClass: 'rsform_dragged',
        onDragStop: function (table, row) {
			tidyOrderDir();
		}
	});
});

RSFormPro.$.formTabs = {
	tabTitles: {},
	tabContents: {},
	
	build: function (startindex) {
		this.each(function (index, el) {
			var tid = RSFormPro.$(el).attr('id');
			RSFormPro.$.formTabs.grabElements(el,tid);
			RSFormPro.$.formTabs.makeTitlesClickable(tid);
			RSFormPro.$.formTabs.setAllContentsInactive(tid);
			RSFormPro.$.formTabs.setTitleActive(startindex,tid);
			RSFormPro.$.formTabs.setContentActive(startindex,tid);
		});
	},
	
	grabElements: function(el,tid) {
		var children = RSFormPro.$(el).children();
		children.each(function(index, child) {			
			if (index == 0)
				RSFormPro.$.formTabs.tabTitles[tid] = RSFormPro.$(child).find('a');
			else if (index == 1)
				RSFormPro.$.formTabs.tabContents[tid] = RSFormPro.$(child).children();
		});
	},
	
	setAllTitlesInactive: function (tid) {
		this.tabTitles[tid].each(function(index, title) {
			RSFormPro.$(title).removeClass('active');
		});
	},
	
	setTitleActive: function (index,tid) {
		index = parseInt(index);
		if (tid == 'rsform_tab3') document.getElementById('ptab').value = index;
		RSFormPro.$(this.tabTitles[tid][index]).addClass('active');
	},
	
	setAllContentsInactive: function (tid) {
		this.tabContents[tid].each(function(index, content) {
			RSFormPro.$(content).hide();
		});
	},
	
	setContentActive: function (index,tid) {
		index = parseInt(index);
		RSFormPro.$(this.tabContents[tid][index]).show();
	},
	
	makeTitlesClickable: function (tid) {
		this.tabTitles[tid].each(function(index, title) {
			RSFormPro.$(title).click(function () {
				RSFormPro.$.formTabs.setAllTitlesInactive(tid);
				RSFormPro.$.formTabs.setTitleActive(index,tid);
				
				RSFormPro.$.formTabs.setAllContentsInactive(tid);
				RSFormPro.$.formTabs.setContentActive(index,tid);
			});
		});
	}
}

RSFormPro.$.fn.extend({
	formTabs: RSFormPro.$.formTabs.build
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

	var params = new Array();
	var orders = document.getElementsByName('dirorder[]');
	var cids = document.getElementsByName('dircid[]');
	var formId = document.getElementById('formId').value;
	
	for (i=0; i<orders.length; i++) {
		params.push('cid[' + cids[i].value + ']=' + parseInt(i + 1));
		orders[i].value = i + 1;
	}
	
	params.push('formId='+formId);
	
	toggleOrderSpansDir();
	
	xml=buildXmlHttp();

	var url = 'index.php?option=com_rsform&task=directory.save.ordering&randomTime=' + Math.random();
	xml.open("POST", url, true);
	
	params = params.join('&');
	
	//Send the proper header information along with the request
	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");

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

function rsfp_autogenerate() {
	stateLoading();
	
	var params = new Array();
	var cids = document.getElementsByName('dirindetails[]');
	var formId = document.getElementById('formId').value;
	
	for (i=0; i<cids.length; i++) {
		if (cids[i].checked)
			params.push('cid[' + cids[i].value + ']=1');
		else
			params.push('cid[' + cids[i].value + ']=0');
	}
	
	params.push('formId='+formId);
	
	xml=buildXmlHttp();

	var url = 'index.php?option=com_rsform&task=directory.save.details&randomTime=' + Math.random();
	xml.open("POST", url, true);
	
	params = params.join('&');
	
	//Send the proper header information along with the request
	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");

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

function rsfp_SelectAll(what) {
	$$('input[name='+what+'[]]').each(function (el) {
		if ($(what+'check').checked) {
			if (!el.checked)
				el.checked = true;
		} else {
			if (el.checked)
				el.checked = false;
		}
	});
}

function toggleQuickAddDirectory() {
	var what = 'none';
	if (document.getElementById('QuickAdd1').style.display == 'none')
		what = '';
	document.getElementById('QuickAdd1').style.display = what; 
}
</script>