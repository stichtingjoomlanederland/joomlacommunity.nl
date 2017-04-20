<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive'); ?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	var form = document.getElementById('adminForm');
	var text = '';

	if(task == 'import.save') {
		ret = true;

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

		if(ret) 
			submitform(task);
		else { alert(text); return false; }
			
		submitform(task);
	}
	return false;
}

function rsc_import(classname) {
	jQuery('#class').val(classname);
	submitform('import.save');
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

			<?php if (RSCommentsHelper::isJ3()) { ?>jQuery('#rsc_columns').find('select').trigger("liszt:updated");<?php } ?>
		}
	});
}

jQuery(document).ready(function(){
	jQuery('dt.advanced').click(function(){jQuery('#method_advanced').val(1);});
	jQuery('dt.simple').click(function(){jQuery('#method_advanced').val(0);});
});
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&controller=import&view=import'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
			<?php $this->tabs->title(JText::_('COM_RSCOMMENTS_IMPORT_SIMPLE'), 'simple'); ?>
			<?php $this->tabs->title(JText::_('COM_RSCOMMENTS_IMPORT_ADVANCED'), 'advanced'); ?>
			<?php $this->tabs->content((!empty($this->html)) ? '<table class="table table-striped adminform">'.implode('',$this->html).'</table>' : JText::_('COM_RSCOMMENTS_IMPORT_PLUGINS_PLEASE')); ?>

			<?php $content = $this->loadTemplate('advanced');
					$this->tabs->content($content);
			?>

			<?php echo $this->tabs->render();?>
		</div> <!-- .span10 -->
	</div> <!-- .row-fluid -->
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="class" id="class" value="" />
</form>
<?php if(!empty($this->table)) { ?> 
<script type="text/javascript">
jQuery('#toolbar-save').css('display','block');
var isadvanced =  true;
</script>
<?php } ?>