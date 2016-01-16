<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

//keep session alive while editing
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=messages'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" autocomplete="off">
	<div class="row-fluid">
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
			<table class="adminlist table table-striped">
			<thead>
				<th><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_TYPE'); ?></th>
			</thead>
			<tbody>
				<tr>
					<td><a class="<?php echo rseventsproHelper::tooltipClass(); ?>" rel="{handler: 'iframe'}" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REGISTRATION_INFO')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=message&type=registration'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REGISTRATION_EMAIL'); ?></a></td>
				</tr>
				<tr>
					<td><a class="<?php echo rseventsproHelper::tooltipClass(); ?>" rel="{handler: 'iframe'}" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CONF_EMAIL_ACTIVATION_INFO')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=message&type=activation'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_ACTIVATION_EMAIL'); ?></a></td>
				</tr>
				<tr>
					<td><a class="<?php echo rseventsproHelper::tooltipClass(); ?>" rel="{handler: 'iframe'}" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CONF_EMAIL_UNSUBSCRIBE_INFO')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=message&type=unsubscribe'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_UNSUBSCRIBE_EMAIL'); ?></a></td>
				</tr>
				<tr>
					<td><a class="<?php echo rseventsproHelper::tooltipClass(); ?>" rel="{handler: 'iframe'}" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CONF_EMAIL_DENIED_INFO')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=message&type=denied'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_DENIED_EMAIL'); ?></a></td>
				</tr>
				<tr>
					<td><a class="<?php echo rseventsproHelper::tooltipClass(); ?>" rel="{handler: 'iframe'}" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CONF_EMAIL_INVITE_INFO')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=message&type=invite'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_INVITE_EMAIL'); ?></a></td>
				</tr>
				<tr>
					<td><a class="<?php echo rseventsproHelper::tooltipClass(); ?>" rel="{handler: 'iframe'}" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REMINDER_INFO')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=message&type=reminder'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REMINDER_EMAIL'); ?></a></td>
				</tr>
				<tr>
					<td><a class="<?php echo rseventsproHelper::tooltipClass(); ?>" rel="{handler: 'iframe'}" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CONF_EMAIL_POSTREMINDER_INFO')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=message&type=preminder'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_PREMINDER_EMAIL'); ?></a></td>
				</tr>
				<tr>
					<td><a class="<?php echo rseventsproHelper::tooltipClass(); ?>" rel="{handler: 'iframe'}" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CONF_EMAIL_MODERATION_INFO')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=message&type=moderation'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_MODERATION_EMAIL'); ?></a></td>
				</tr>
				<tr>
					<td><a class="<?php echo rseventsproHelper::tooltipClass(); ?>" rel="{handler: 'iframe'}" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CONF_EMAIL_TAG_MODERATION_INFO')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=message&type=tag_moderation'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_TAG_MODERATION_EMAIL'); ?></a></td>
				</tr>
				<tr>
					<td><a class="<?php echo rseventsproHelper::tooltipClass(); ?>" rel="{handler: 'iframe'}" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CONF_EMAIL_NOTIFICATION_INFO')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=message&type=notify_me'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_NOTIFICATION_EMAIL'); ?></a></td>
				</tr>
				<tr>
					<td><a class="<?php echo rseventsproHelper::tooltipClass(); ?>" rel="{handler: 'iframe'}" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REPORT_INFO')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=message&type=report'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_REPORT_EMAIL'); ?></a></td>
				</tr>
				<tr>
					<td><a class="<?php echo rseventsproHelper::tooltipClass(); ?>" rel="{handler: 'iframe'}" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CONF_EMAIL_APPROVAL_INFO')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=message&type=approval'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_CONF_EMAIL_APPROVAL_EMAIL'); ?></a></td>
				</tr>
			</tbody>
		</table>
		</div>
	</div>
	
	<input type="hidden" name="task" value="" />
</form>