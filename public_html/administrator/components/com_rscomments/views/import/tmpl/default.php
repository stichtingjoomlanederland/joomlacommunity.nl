<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive'); ?>

<script type="text/javascript">
function rsc_import_table() {
	var text = '';
	var ret = true;

	if (jQuery('#jform_rsc_col_option').val()=='') {
		jQuery('#jform_rsc_col_option').toggleClass('rs_sel rsc_error', true); 
		ret=false; 
		text += "<?php echo JText::_('COM_RSCOMMENTS_IMPORT_NO_OPTION',true); ?> \n";
	} else jQuery('#jform_rsc_col_option').toggleClass('rs_sel rsc_error', false); 

	if (jQuery('#jform_rsc_col_id').val()=='') {	
		jQuery('#jform_rsc_col_id').toggleClass('rs_sel rsc_error', true);
		ret=false; 
		text += "<?php echo JText::_('COM_RSCOMMENTS_IMPORT_NO_OPTIONID',true); ?> \n";
	} else jQuery('#jform_rsc_col_id').toggleClass('rs_sel rsc_error', false); 

	if (jQuery('#jform_rsc_col_name').val()=='') {
		jQuery('#jform_rsc_col_name').toggleClass('rs_sel rsc_error', true);
		ret=false; 
		text += "<?php echo JText::_('COM_RSCOMMENTS_IMPORT_NO_NAME',true); ?> \n";
	} else jQuery('#jform_rsc_col_name').toggleClass('rs_sel rsc_error', false); 

	if (jQuery('#jform_rsc_col_email').val()=='') {
		jQuery('#jform_rsc_col_email').toggleClass('rs_sel rsc_error', true);
		ret=false; 
		text += "<?php echo JText::_('COM_RSCOMMENTS_IMPORT_NO_EMAIL',true); ?> \n";
	} else jQuery('#jform_rsc_col_email').toggleClass('rs_sel rsc_error', false); 

	if (jQuery('#jform_rsc_col_comment').val()=='') {
		jQuery('#jform_rsc_col_comment').toggleClass('rs_sel rsc_error', true); 
		ret=false; 
		text += "<?php echo JText::_('COM_RSCOMMENTS_IMPORT_NO_COMMENT',true); ?> \n";
	} else jQuery('#jform_rsc_col_comment').toggleClass('rs_sel rsc_error', false); 

	if (ret) {
		Joomla.submitform('import.save');
	} else { 
		alert(text);
	}
}

function rsc_import(classname) {
	jQuery('#class').val(classname);
	Joomla.submitform('import.save');
}

function rsc_update_cols(value) {
	jQuery.ajax({
		type: 'POST',
		url: "index.php?option=com_rscomments&task=import.getcolumns&table="+value,
		success: function(data)	{
			var response = jQuery.parseJSON(data);

			var html = '<option value=""><?php echo JText::_('COM_RSCOMMENTS_SELECT_COLUMN');?></option>';
			jQuery(response).each(function(index, column){
				html += '<option value="'+column+'">'+column+'</option>';
			});

			jQuery('#rsc_columns').find('select').each(function(select_key,select){
				jQuery(select).empty().html(html);
			});

			jQuery('#rsc_columns').find('select').trigger("liszt:updated");
		}
	});
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&controller=import&view=import'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<?php echo RSCommentsAdapterGrid::sidebar(); ?>
		<?php $this->tabs->addTitle(JText::_('COM_RSCOMMENTS_IMPORT_SIMPLE'), 'simple'); ?>
		<?php $this->tabs->addTitle(JText::_('COM_RSCOMMENTS_IMPORT_ADVANCED'), 'advanced'); ?>
		<?php $this->tabs->addContent((!empty($this->html)) ? '<table class="table table-striped adminform">'.implode('',$this->html).'</table>' : '<div class="alert alert-warning">'.JText::_('COM_RSCOMMENTS_IMPORT_PLUGINS_PLEASE').'</div>'); ?>
		<?php $this->tabs->addContent($this->loadTemplate('advanced')); ?>
		<?php echo $this->tabs->render();?>
	</div>
	
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="class" id="class" value="" />
</form>