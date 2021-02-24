<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

//keep session alive while editing
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=message&type='.$this->type); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" autocomplete="off">
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(7); ?>">
			<?php echo $this->form->renderField('language'); ?>
			<?php if (!in_array($this->type, array('tag_moderation','moderation','notify_me'))) echo $this->form->renderField($this->type.'_enable'); ?>
			<?php echo $this->form->renderField($this->type.'_subject'); ?>
			<?php echo $this->form->renderField($this->type.'_mode'); ?>
			<?php if ($this->type == 'report') echo $this->form->renderField('report_to','config'); ?>
			<?php if ($this->type == 'report') echo $this->form->renderField('report_to_owner','config'); ?>
			<?php if ($this->type == 'invite') echo $this->form->renderField('email_invite_message','config'); ?>
			<?php if ($this->type == 'preminder') echo $this->form->renderField('auto_postreminder','config'); ?>
			<?php if ($this->type == 'preminder') echo $this->form->renderField('postreminder_hash','config'); ?>
			<?php if ($this->type == 'preminder') echo RSEventsproAdapterGrid::renderField('&nbsp;','<a id="preminderurl" target="_blank" href="'.JURI::root().'index.php?option=com_rseventspro&amp;task=autopostreminder&amp;hash='.rseventsproHelper::getConfig('postreminder_hash').'">'.JText::_('COM_RSEVENTSPRO_MESSAGE_POST_REMINDER_URL').'</a>'); ?>
			
			<?php if ($this->type == 'reminder') { ?>
			<table id="reminderEmailTable" class="table table-striped table-bordered">
				<tr>
					<td>
						<?php echo JText::sprintf('COM_RSEVENTSPRO_MESSAGE_REMINDER_INFO',$this->form->getInput('email_reminder_days','config'),$this->form->getInput('email_reminder_run','config')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<a href="<?php echo JURI::root(); ?>index.php?option=com_rseventspro&amp;task=autoreminder"><?php echo JText::_('COM_RSEVENTSPRO_MESSAGE_REMINDER_URL'); ?></a>
					</td>
				</tr>
			</table>
			<?php } ?>
			
			<?php echo $this->form->getInput($this->type.'_message'); ?>
		</div>
		<div class="<?php echo RSEventsproAdapterGrid::column(5); ?>">
			<?php if ($this->placeholders) { ?>
			<fieldset class="options-form">
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EMAIL_PLACEHOLDERS'); ?></legend>
				<table class="table table-striped table-condensed" id="placeholdersTable" width="100%">
				<?php foreach ($this->placeholders as $placeholder => $description) { ?>
				<tr>
					<td class="rsepro-placeholder"><?php echo $placeholder; ?></td>
					<td><?php echo JText::_($description); ?></td>
				</tr>
				<?php } ?>
				</table>
			</fieldset>
			<?php } ?>
		</div>
	</div>
	
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="jform[type]" value="<?php echo $this->type; ?>" />
</form>

<?php if ($this->type == 'preminder') { ?>
<script type="text/javascript">
window.addEventListener('DOMContentLoaded', function() {
	jQuery('#jform_config_postreminder_hash').on('keyup', function() {
		jQuery('#preminderurl').prop('href','<?php echo JURI::root(); ?>index.php?option=com_rseventspro&amp;task=autopostreminder&amp;hash='+ jQuery(this).val());
	});
});
</script>
<?php } ?>