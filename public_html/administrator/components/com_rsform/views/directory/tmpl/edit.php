<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive');
JHtml::script('com_rsform/admin/directory.js', array('relative' => true, 'version' => 'auto'));
?>

<form action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	
	<div class="alert alert-error" id="rsform_layout_msg" <?php if ($this->directory->ViewLayoutAutogenerate) { ?>style="display: none"<?php } ?>>
		<?php echo JText::_('RSFP_SUBM_DIR_AUTOGENERATE_LAYOUT_DISABLED'); ?>
	</div>
	<br />
	
	<div id="rsform_container">
		<div id="state" style="display: none;"><?php echo JHtml::image('com_rsform/admin/load.gif', JText::_('RSFP_PROCESSING'), null, true); ?><?php echo JText::_('RSFP_PROCESSING'); ?></div>
		
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
jQuery(document).ready(function(){
	jQuery('#rsform_tab3').formTabs(<?php echo $this->tab; ?>);
	jQuery('#dirSubmissionsTable tbody').tableDnD({
		onDragClass: 'rsform_dragged',
        onDragStop: function (table, row) {
			tidyOrderDir();
		}
	});
});
</script>