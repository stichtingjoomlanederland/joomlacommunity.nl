<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

//keep session alive while editing
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&tmpl=component&type='.$this->type); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" autocomplete="off">
	<div class="row-fluid">
		<div class="span12">
			<div style="width:100%;text-align:right;">
				<button type="button" onclick="Joomla.submitbutton('messages.save');" class="btn btn-primary button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE_BTN'); ?></button>
				<button type="button" onclick="window.parent.SqueezeBox.close();" class="btn button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
			</div>
			<?php 
				echo JHtml::_('rsfieldset.start', 'adminform', JText::_('COM_RSEVENTSPRO_MESSAGE_'.strtoupper($this->type)));
				echo JHtml::_('rsfieldset.element', $this->form->getLabel('language'), $this->form->getInput('language'));
				
				if (!in_array($this->type, array('tag_moderation','moderation','notify_me')))
					echo JHtml::_('rsfieldset.element', $this->form->getLabel($this->type.'_enable'), $this->form->getInput($this->type.'_enable'));
				
				echo JHtml::_('rsfieldset.element', $this->form->getLabel($this->type.'_subject'), $this->form->getInput($this->type.'_subject'));
				echo JHtml::_('rsfieldset.element', $this->form->getLabel($this->type.'_mode'), $this->form->getInput($this->type.'_mode'));
				
				if ($this->type == 'report') {
					echo JHtml::_('rsfieldset.element', $this->form->getLabel('report_to','config'), $this->form->getInput('report_to','config'));
					echo JHtml::_('rsfieldset.element', $this->form->getLabel('report_to_owner','config'), $this->form->getInput('report_to_owner','config'));
				}
				
				if ($this->type == 'invite')
					echo JHtml::_('rsfieldset.element', $this->form->getLabel('email_invite_message','config'), $this->form->getInput('email_invite_message','config'));
				
				if ($this->type == 'preminder') {
					$URL = '<a id="preminderurl" target="_blank" href="'.JURI::root().'index.php?option=com_rseventspro&amp;task=autopostreminder&amp;hash='.rseventsproHelper::getConfig('postreminder_hash').'">'.JText::_('COM_RSEVENTSPRO_MESSAGE_POST_REMINDER_URL').'</a>';
					
					echo JHtml::_('rsfieldset.element', $this->form->getLabel('auto_postreminder','config'), $this->form->getInput('auto_postreminder','config'));
					echo JHtml::_('rsfieldset.element', $this->form->getLabel('postreminder_hash','config'), $this->form->getInput('postreminder_hash','config'));
					echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<span class="rsextra">'.$URL.'</span>');
				}
				
				echo JHtml::_('rsfieldset.end');
			?>
			<?php if ($this->type == 'reminder') { ?>
			<div class="clr"></div>
			<?php echo JText::sprintf('COM_RSEVENTSPRO_MESSAGE_REMINDER_INFO',$this->form->getInput('email_reminder_days','config'),$this->form->getInput('email_reminder_run','config')); ?>
			<a href="<?php echo JURI::root(); ?>index.php?option=com_rseventspro&amp;task=autoreminder"><?php echo JText::_('COM_RSEVENTSPRO_MESSAGE_REMINDER_URL'); ?></a>
			<br /><br />
			<?php } ?>
			
			<div class="clr"></div>
			<?php echo $this->form->getInput($this->type.'_message'); ?>
			<div>
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="jform[type]" value="<?php echo $this->type; ?>" />
			</div>
		</div>
	</div>
</form>

<?php if ($this->type == 'preminder') { ?>
<script type="text/javascript">
window.addEvent('domready', function() {
	$('jform_config_postreminder_hash').addEvent('keyup', function() {
		$('preminderurl').href = '<?php echo JURI::root(); ?>index.php?option=com_rseventspro&amp;task=autopostreminder&amp;hash='+this.value;
	});
});
</script>
<?php } ?>