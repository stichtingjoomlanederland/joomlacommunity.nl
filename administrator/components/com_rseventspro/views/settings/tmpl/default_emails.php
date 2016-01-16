<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>

<div>
	<div class="rsemaillft span4">
		<?php 
		$fieldsets = array('emails'); 
		foreach ($fieldsets as $fieldset) {
			echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets[$fieldset]->label));
			foreach ($this->form->getFieldset($fieldset) as $field) {
				echo JHtml::_('rsfieldset.element', $field->label, $field->input);
			}
			echo JHtml::_('rsfieldset.end');
		}
		?>
	</div>
	<div class="rsemailrgt span8">
		<table class="adminlist table table-striped">
			<thead>
				<th><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_TYPE'); ?></th>
				<th width="3%"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_EDIT'); ?></th>
			<thead>
			<tbody>
				<tr>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REGISTRATION_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=registration&tmpl=component'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REGISTRATION_EMAIL'); ?></a></td>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REGISTRATION_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=registration&tmpl=component'); ?>"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/edit.png" alt="" /></a></td>
				</tr>
				<tr>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_ACTIVATION_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=activation&tmpl=component'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_ACTIVATION_EMAIL'); ?></a></td>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_ACTIVATION_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=activation&tmpl=component'); ?>"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/edit.png" alt="" /></a></td>
				</tr>
				<tr>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_UNSUBSCRIBE_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=unsubscribe&tmpl=component'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_UNSUBSCRIBE_EMAIL'); ?></a></td>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_UNSUBSCRIBE_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=unsubscribe&tmpl=component'); ?>"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/edit.png" alt="" /></a></td>
				</tr>
				<tr>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_DENIED_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=denied&tmpl=component'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_DENIED_EMAIL'); ?></a></td>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_DENIED_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=denied&tmpl=component'); ?>"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/edit.png" alt="" /></a></td>
				</tr>
				<tr>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_INVITE_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=invite&tmpl=component'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_INVITE_EMAIL'); ?></a></td>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_INVITE_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=invite&tmpl=component'); ?>"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/edit.png" alt="" /></a></td>
				</tr>
				<tr>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REMINDER_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=reminder&tmpl=component'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REMINDER_EMAIL'); ?></a></td>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REMINDER_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=reminder&tmpl=component'); ?>"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/edit.png" alt="" /></a></td>
				</tr>
				<tr>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_POSTREMINDER_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=preminder&tmpl=component'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_PREMINDER_EMAIL'); ?></a></td>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_POSTREMINDER_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=preminder&tmpl=component'); ?>"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/edit.png" alt="" /></a></td>
				</tr>
				<tr>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_MODERATION_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=moderation&tmpl=component'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_MODERATION_EMAIL'); ?></a></td>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_MODERATION_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=moderation&tmpl=component'); ?>"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/edit.png" alt="" /></a></td>
				</tr>
				<tr>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_TAG_MODERATION_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=tag_moderation&tmpl=component'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_TAG_MODERATION_EMAIL'); ?></a></td>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_TAG_MODERATION_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=tag_moderation&tmpl=component'); ?>"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/edit.png" alt="" /></a></td>
				</tr>
				<tr>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_NOTIFICATION_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=notify_me&tmpl=component'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_NOTIFICATION_EMAIL'); ?></a></td>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_NOTIFICATION_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=notify_me&tmpl=component'); ?>"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/edit.png" alt="" /></a></td>
				</tr>
				<tr>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REPORT_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=report&tmpl=component'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REPORT_EMAIL'); ?></a></td>
					<td><a class="modal hasTip" rel="{handler: 'iframe'}" title="<?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REPORT_INFO'); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages&type=report&tmpl=component'); ?>"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/edit.png" alt="" /></a></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>