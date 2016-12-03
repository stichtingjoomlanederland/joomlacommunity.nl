<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.modal'); ?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'group.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	
	jQuery(document).ready(function(){
		JHide();
		<?php if ($this->used) { ?>
		var used = new String('<?php echo implode(',',$this->used); ?>');
		var array = used.split(','); 
		
		jQuery('#<?php echo rseventsproHelper::isJ3() ? 'jform_jgroups' : 'jformjgroups'; ?> option').each(function(){
			if (array.contains(jQuery(this).val())) {
				jQuery(this).prop('disabled', true);
			}
		});
		jQuery('#<?php echo rseventsproHelper::isJ3() ? 'jform_jgroups' : 'jformjgroups'; ?>').trigger("liszt:updated");
		<?php } ?>
	});
	
	function JHide() {
		if (jQuery('input[name="jform[can_post_events]"]:checked').val() == 1) {
			<?php if (rseventsproHelper::isJ3()) { ?>
			jQuery('fieldset[id=jform_can_repeat_events]').parent().parent().css('display','');
			jQuery('fieldset[id=jform_event_moderation]').parent().parent().css('display','');
			<?php } else { ?>
			jQuery('fieldset[id=jform_can_repeat_events]').parent().css('display','');
			jQuery('fieldset[id=jform_event_moderation]').parent().css('display','');
			<?php } ?>
		} else {
			<?php if (rseventsproHelper::isJ3()) { ?>
			jQuery('fieldset[id=jform_can_repeat_events]').parent().parent().css('display','none');
			jQuery('fieldset[id=jform_event_moderation]').parent().parent().css('display','none');
			<?php } else { ?>
			jQuery('fieldset[id=jform_can_repeat_events]').parent().css('display','none');
			jQuery('fieldset[id=jform_event_moderation]').parent().css('display','none');
			<?php } ?>
		}
	}
	
	function rsepro_OpenUsersModal() {
		SqueezeBox.open('index.php?option=com_users&view=users&layout=modal&tmpl=component&field=jform_jusers<?php echo !empty($this->excludes) ? ('&excluded=' . base64_encode(json_encode($this->excludes))) : ''; ?>', {
			handler : 'iframe',
			size : {
				x : 800,
				y : 500
			},
			iframeOptions: {
				onload : 'rsepro_attachUsersModalEvents(this)'
			}
		});
	}
	
	function rsepro_attachUsersModalEvents(what) {
		jQuery(what).contents().find('a.button-select').each(function() {
			jQuery(this).on('click', function() {
				jSelectUser_jform_jusers(jQuery(this).data('user-value'), jQuery(this).data('user-name'));
			});
		});
	}
	
	function jSelectUser_jform_jusers(id, name) {
		if (id == '') {
			SqueezeBox.close();
			return;
		}
		
		if (jQuery('#jform_jusers option[value="'+id+'"]').length) {
			alert('<?php echo JText::_('COM_RSEVENTSPRO_USER_ALREADY_EXISTS',true); ?>');
			return;
		}
		
		jQuery('#jform_jusers').append(jQuery('<option>', { 'text': name, 'value': id, selected : true }));
		jQuery('#jform_jusers').trigger("liszt:updated");
		SqueezeBox.close();
	}
	
	function removeusers() {
		jQuery('#jform_jusers option').remove();
		jQuery('#jform_jusers').trigger("liszt:updated");
	}
</script>

<?php if (!rseventsproHelper::isJ3()) { ?>
<style type="text/css">
.rsfieldsetfix {overflow: visible !important;}
</style>
<?php } ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=group&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span12">
			<?php $extra = '<span class="rsextra"><a href="javascript:void(0)" onclick="rsepro_OpenUsersModal();">'.JText::_('COM_RSEVENTSPRO_GROUP_ADD_USERS').'</a>'; ?>
			<?php $extra .= ' / <a href="javascript:void(0);" onclick="removeusers();">'.JText::_('COM_RSEVENTSPRO_GROUP_REMOVE_USERS').'</a></span>'; ?>
			
			<?php echo JHtml::_('rsfieldset.start', 'adminform rsfieldsetfix'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('name'), $this->form->getInput('name')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('jgroups'), $this->form->getInput('jgroups')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('jusers'), $this->form->getInput('jusers').$extra); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
		</div>
		
		<?php 
			$this->tabs->title('COM_RSEVENTSPRO_GROUP_PERMISSIONS', 'group');
			
			// prepare the content
			$content = $this->loadTemplate('general');
			
			// add the tab content
			$this->tabs->content($content);
			
			$this->tabs->title('COM_RSEVENTSPRO_EVENT_OPTIONS', 'event');
			
			// prepare the content
			$content = $this->loadTemplate('event');
			
			// add the tab content
			$this->tabs->content($content);
			
			echo $this->tabs->render(); 
		?>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>