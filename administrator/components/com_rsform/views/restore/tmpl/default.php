<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JText::script('RSFP_ERROR');
JText::script('RSFP_STATUS');
JText::script('RSFP_DECOMPRESSING_ARCHIVE');
JText::script('RSFP_READING_METADATA_INFORMATION');
JText::script('RSFP_REMOVING_OLD_FORMS');
JText::script('RSFP_RESTORING_FORM_STRUCTURE');
JText::script('RSFP_RESTORE_COMPLETE');
JText::script('RSFP_RESTORING_FORM_SUBMISSIONS');

JText::script('RSFP_BACKUP_INFORMATION');
JText::script('RSFP_BACKUP_OS');
JText::script('RSFP_BACKUP_WEBSITE');
JText::script('RSFP_BACKUP_AUTHOR');
JText::script('RSFP_BACKUP_DATE');
JText::script('RSFP_DELETING_TEMPORARY_FOLDER');
JText::script('RSFP_TMP_FOLDER_REMOVED');
JText::script('RSFP_JSON_DECODING_ERROR');
?>
<form enctype="multipart/form-data" action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10 rsf-backup-restore">
		<div class="progressWrapper"><div class="progressBar" id="progressBar">0%</div></div>
		<a class="btn btn-primary" style="display: none;" id="viewForms" href="<?php echo JRoute::_('index.php?option=com_rsform&view=forms'); ?>"><?php echo JText::_('RSFP_MANAGE_FORMS'); ?></a>
	</div>
	<div>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="option" value="com_rsform"/>
		<input type="hidden" name="boxchecked" value="0"/>
		
		<input type="hidden" name="key" id="restoreKey" value="<?php echo $this->key; ?>" />
		<input type="hidden" name="overwrite" id="overwriteOption" value="<?php echo $this->overwrite; ?>" />
		<input type="hidden" name="keepid" id="keepIdOption" value="<?php echo $this->keepId; ?>" />
	</div>
</form>

<script type="text/javascript">
RSFormPro.Restore.requestTimeOut.Seconds = <?php echo $this->config->get('request_timeout');?>;
RSFormPro.Restore.start();
</script>