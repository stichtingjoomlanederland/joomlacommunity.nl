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
		if (task == 'group.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	
	jQuery(document).ready(function() {
		<?php if ($this->used) { ?>
		var used = new String('<?php echo implode(',',$this->used); ?>');
		var array = used.split(','); 
		
		jQuery('#jform_gid option').each(function(){
			if (array.includes(jQuery(this).val())) {
				jQuery(this).prop('disabled', true);
			}
		});
		jQuery('#jform_gid').trigger("liszt:updated");
		<?php } ?>
		<?php if (empty($this->item->IdGroup)) { ?>
		jQuery('#jform_gid option').each(function(){
			if (jQuery(this).is(':disabled') != true) {
				jQuery(this).prop('selected', true);
			}
		});
		jQuery('#jform_gid').trigger("liszt:updated");
		<?php } ?>
	});
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&view=group&layout=edit&IdGroup='.(int) $this->item->IdGroup); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span12">
		<?php
			$this->tabs->title(JText::_('COM_RSCOMMENTS_GROUP_DETAILS'), 'general');
			$content = $this->loadTemplate('general');
			$this->tabs->content($content);
			
			$this->tabs->title(JText::_('COM_RSCOMMENTS_GROUPS_SETTINGS_BBCODE'), 'bbcode');
			$content = $this->loadTemplate('bbcode');
			$this->tabs->content($content);

			echo $this->tabs->render();
		?>
		</div>
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="task" value="" />
		<?php echo $this->form->getInput('IdGroup'); ?>
	</div>
</form>