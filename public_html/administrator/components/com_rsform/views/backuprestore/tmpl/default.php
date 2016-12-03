<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
JText::script('RSFP_BACKUP_SELECT');
JText::script('RSFP_ERROR');
JText::script('RSFP_STATUS');
JText::script('RSFP_STATUS_BACKING_UP_FORM_STRUCTURE_LEFT');
JText::script('RSFP_STATUS_BACKING_UP_FORM_SUBMISSIONS_LEFT');
JText::script('RSFP_STATUS_FINISHING_UP_SUBMISSIONS_FOR_FORM');
JText::script('RSFP_STATUS_COMPRESSING_FILES');
JText::script('RSFP_OVERWRITE_WARNING');
JText::script('RSFP_JSON_DECODING_ERROR');
?>
<form enctype="multipart/form-data" action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10 rsf-backup-restore">
	<?php
	// add the title
	$this->tabs->addTitle(JText::_('RSFP_BACKUP'), 'backup');
		
	// prepare the content
	$content = $this->loadTemplate('backup');
		
	// add the tab content
	$this->tabs->addContent($content);
	
	// add the title
	$this->tabs->addTitle(JText::_('RSFP_RESTORE'), 'restore');
		
	// prepare the content
	$content = $this->loadTemplate('restore');
		
	// add the tab content
	$this->tabs->addContent($content);
	
	// render tabs
	$this->tabs->render();
	?>
	</div>
	
	<div>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="option" value="com_rsform"/>
		<input type="hidden" name="boxchecked" value="0"/>
		
		<input type="hidden" name="key" id="backupKey" value="" />
	</div>
</form>

<script type="text/javascript">
function submitbutton(task)
{
	if (task == 'backup.start') {
		if (document.adminForm.boxchecked.value == 0) {
			return alert(Joomla.JText._('RSFP_BACKUP_SELECT'));
		} else {
			return RSFormPro.Backup.start();
		}
	} else if (task == 'backup.download') {
		document.getElementById('backupKey').value = RSFormPro.Backup.key;
	} else if (task == 'restore.start') {
		if (jQuery('#jform_overwrite1').prop('checked') && !confirm(Joomla.JText._('RSFP_OVERWRITE_WARNING'))) {
			return;
		}
	}
	
	Joomla.submitform(task);
}

Joomla.submitbutton = submitbutton;
</script>