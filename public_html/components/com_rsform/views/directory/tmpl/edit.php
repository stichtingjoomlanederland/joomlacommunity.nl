<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access'); 

JHtml::_('behavior.keepalive');
?>

<script type="text/javascript">
function directorySave(task) {
	var form = document.getElementById('directoryEditForm');
	form.task.value = task;
	form.submit();
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rsform&view=directory&layout=edit&id='.$this->app->input->getInt('id',0)); ?>" method="post" name="adminForm" id="directoryEditForm" enctype="multipart/form-data">
	<table class="table table-condensed table-striped table-hover table-bordered category">
		<?php foreach ($this->fields as $field) { ?>
		<tr>
			<td width="200" style="width: 200px;">
				<?php echo $field[0]; ?> <?php echo $field[2]; ?>
			</td>
			<td>
				<?php echo $field[1]; ?>
			</td>
		</tr>
		<?php } ?>
	</table>
	
	<div class="form-actions">
		<button type="button" onclick="directorySave('apply');" class="btn btn-primary button"><?php echo JText::_('RSFP_SUBM_DIR_APPLY'); ?></button> 
		<button type="button" onclick="directorySave('save');" class="btn btn-primary button"><?php echo JText::_('RSFP_SUBM_DIR_SAVE'); ?></button> 
		<button type="button" onclick="directorySave('back')" class="btn button"><?php echo JText::_('RSFP_SUBM_DIR_BACK'); ?></button>
	</div>
	
	<input type="hidden" name="option" value="com_rsform">
	<input type="hidden" name="controller" value="directory">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="id" value="<?php echo $this->app->input->getInt('id',0); ?>">
	<input type="hidden" name="formId" value="<?php echo $this->params->get('formId'); ?>">
	<input type="hidden" name="form[formId]" value="<?php echo $this->params->get('formId'); ?>">
</form>