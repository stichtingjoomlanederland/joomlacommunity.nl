<?php
/**
* @version 1.0.0
* @package RSSeo! 1.0.0
* @copyright (C) 2009-2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro'); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
<div class="row-fluid">
	<div class="width-<?php echo $this->middle ? 30 : 70; ?> fltlft">
		<div class="dashboard-container">
			<div class="rsspan2">
				<div class="dashboard-wraper">
					<div class="dashboard-content"> 
						<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=events'); ?>"> 
							<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/dashboard/events.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_EVENTS'); ?>" />
							<span class="dashboard-title"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_EVENTS'); ?></span> 
						</a> 
					</div>
				</div>
			</div>
			<div class="rsspan2">
				<div class="dashboard-wraper">
					<div class="dashboard-content"> 
						<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=locations'); ?>"> 
							<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/dashboard/locations.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_LOCATIONS'); ?>" />
							<span class="dashboard-title"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_LOCATIONS'); ?></span> 
						</a> 
					</div>
				</div>
			</div>
			<div class="rsspan2">
				<div class="dashboard-wraper">
					<div class="dashboard-content"> 
						<a href="<?php echo JRoute::_('index.php?option=com_categories&extension=com_rseventspro'); ?>"> 
							<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/dashboard/categories.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_CATEGORIES'); ?>" />
							<span class="dashboard-title"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_CATEGORIES'); ?></span> 
						</a> 
					</div>
				</div>
			</div>
			<div class="rsspan2">
				<div class="dashboard-wraper">
					<div class="dashboard-content"> 
						<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=tags'); ?>"> 
							<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/dashboard/tags.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_TAGS'); ?>" />
							<span class="dashboard-title"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_TAGS'); ?></span> 
						</a> 
					</div>
				</div>
			</div>
			<div class="rsspan2">
				<div class="dashboard-wraper">
					<div class="dashboard-content"> 
						<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions'); ?>"> 
							<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/dashboard/subscriptions.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIPTIONS'); ?>" />
							<span class="dashboard-title"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIPTIONS'); ?></span> 
						</a> 
					</div>
				</div>
			</div>
			<div class="rsspan2">
				<div class="dashboard-wraper">
					<div class="dashboard-content"> 
						<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=payments'); ?>"> 
							<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/dashboard/payments.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_PAYMENTS'); ?>" />
							<span class="dashboard-title"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_PAYMENTS'); ?></span> 
						</a> 
					</div>
				</div>
			</div>
			<div class="rsspan2">
				<div class="dashboard-wraper">
					<div class="dashboard-content"> 
						<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=groups'); ?>"> 
							<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/dashboard/groups.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_GROUPS'); ?>" />
							<span class="dashboard-title"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_GROUPS'); ?></span> 
						</a> 
					</div>
				</div>
			</div>
			<div class="rsspan2">
				<div class="dashboard-wraper">
					<div class="dashboard-content"> 
						<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=imports'); ?>"> 
							<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/dashboard/imports.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_IMPORTS'); ?>" />
							<span class="dashboard-title"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_IMPORTS'); ?></span> 
						</a> 
					</div>
				</div>
			</div>
			<div class="rsspan2">
				<div class="dashboard-wraper">
					<div class="dashboard-content"> 
						<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=settings'); ?>"> 
							<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/dashboard/settings.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SETTINGS'); ?>" />
							<span class="dashboard-title"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SETTINGS'); ?></span> 
						</a> 
					</div>
				</div>
			</div>
			<?php if ($this->config->dashboard_sync) { ?>
			<div class="rsspan2">
				<div class="dashboard-wraper">
					<div class="dashboard-content"> 
						<a href="javascript:void(0)" onclick="Joomla.submitbutton('settings.google')"> 
							<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/dashboard/sync.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SYNC_GOOGLE'); ?>" />
							<span class="dashboard-title"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SYNC_GOOGLE'); ?></span> 
						</a> 
					</div>
				</div>
			</div>
			<div class="rsspan2">
				<div class="dashboard-wraper">
					<div class="dashboard-content"> 
						<a href="javascript:void(0)" onclick="Joomla.submitbutton('settings.facebook')"> 
							<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/dashboard/sync.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SYNC_FACEBOOK'); ?>" />
							<span class="dashboard-title"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SYNC_FACEBOOK'); ?></span> 
						</a> 
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>

	<?php if ($this->middle) { ?>
	<div class="width-35 fltlft">
		<?php if ($this->config->dashboard_upcoming) { ?>
		<div class="dashboard-block">
			<div class="dashboard-block-head">
				<h5><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_UPCOMING_EVENTS'); ?></h5>
			</div>
			<div class="dashboard-block-content">
				<div class="dashboard-block-box">
					<table class="dashboard-block-table task-tbl">
						<thead>
							<tr>
								<th><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_EVENT'); ?></th>
								<th><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIBERS'); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php if (!empty($this->events)) { ?>
						<?php foreach ($this->events as $event) { ?>
						<?php if (!$event->id) continue; ?>
							<tr>
								<td>
									<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$event->id); ?>"><?php echo $event->name; ?></a>
									(<?php echo rseventsproHelper::date($event->start,null,true); ?> - <?php echo rseventsproHelper::date($event->end,null,true); ?>)
								</td>
								<td><?php echo $event->subscribers; ?></td>
							</tr>
						<?php }} ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php } ?>
		
		<?php if ($this->config->dashboard_subscribers) { ?>
		<div class="dashboard-block">
			<div class="dashboard-block-head">
				<h5><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIBERS'); ?></h5>
			</div>
			<div class="dashboard-block-content">
				<div class="dashboard-block-box">
					<table class="dashboard-block-table task-tbl">
						<thead>
							<tr>
								<th><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_EVENT'); ?></th>
								<th><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIBER_NAME'); ?></th>
								<th><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIBER_DATE'); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php if (!empty($this->subscribers)) { ?>
						<?php foreach ($this->subscribers as $subscriber) { ?>
							<tr>
								<td>
									<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$subscriber->eid); ?>"><?php echo $subscriber->ename; ?></a>
								</td>
								<td align="center"><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=subscription.edit&id='.$subscriber->id); ?>"><?php echo $subscriber->name; ?></a></td>
								<td align="center"><?php echo rseventsproHelper::date($subscriber->date,null,true); ?></td>
							</tr>
						<?php }} ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php } ?>
		
		<?php if ($this->config->dashboard_comments && !in_array($this->config->event_comment, array(0,1))) { ?>
		<div class="dashboard-block">
			<div class="dashboard-block-head">
				<h5><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_COMMENTS'); ?></h5>
			</div>
			<div class="dashboard-block-content">
				<div class="dashboard-block-box">
					<table class="dashboard-block-table task-tbl">
						<thead>
							<tr>
								<th><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_EVENT'); ?></th>
								<th><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_COMMENT_NAME'); ?></th>
								<th><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_COMMENT_DATE'); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php if (!empty($this->comments)) { ?>
						<?php foreach ($this->comments as $comment) { ?>
							<tr>
								<td>
									<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=subscription.edit&id='.$comment->id); ?>"><?php echo $comment->name; ?></a>
								</td>
								<td align="center"><?php echo $comment->comment; ?></td>
								<td align="center"><?php echo rseventsproHelper::date($comment->date,null,true); ?></td>
							</tr>
						<?php }} ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
	
	<div class="width-30 fltrt">
		<div class="dashboard-container">
			<div class="dashboard-info">
				<span>
					<img src="<?php echo JURI::root(true); ?>/administrator/components/com_rseventspro/assets/images/rseventspro.png" align="middle" alt="RSEvents!Pro" />
				</span>
				<table class="dashboard-table">
					<tr>
						<td nowrap="nowrap"><strong><?php echo JText::_('COM_RSEVENTSPRO_PRODUCT_VERSION') ?>: </strong></td>
						<td nowrap="nowrap"><b>RSEvents!Pro <?php echo RSEPRO_RS_VERSION; ?> Rev <?php echo RSEPRO_RS_REVISION; ?></b></td>
					</tr>
					<tr>
						<td nowrap="nowrap"><strong><?php echo JText::_('COM_RSEVENTSPRO_COPYRIGHT_NAME') ?>: </strong></td>
						<td nowrap="nowrap">&copy; 2007 - <?php echo date('Y'); ?> <a href="http://www.rsjoomla.com" target="_blank">RSJoomla.com</a></td>
					</tr>
					<tr>
						<td nowrap="nowrap"><strong><?php echo JText::_('COM_RSEVENTSPRO_LICENSE_NAME') ?>: </strong></td>
						<td nowrap="nowrap">GPL Commercial License</a></td>
					</tr>
					<tr>
						<td nowrap="nowrap"><strong><?php echo JText::_('COM_RSEVENTSPRO_CODE_FOR_UPDATE') ?>: </strong></td>
						<?php if (strlen($this->code) == 20) { ?>
						<td nowrap="nowrap" class="correct-code"><?php echo $this->escape($this->code); ?></td>
						<?php } elseif ($this->code) { ?>
						<td nowrap="nowrap" class="incorrect-code"><?php echo $this->escape($this->code); ?></td>
						<?php } else { ?>
						<td nowrap="nowrap" class="missing-code">
							<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=settings'); ?>">
								<?php echo JText::_('COM_RSEVENTSPRO_PLEASE_ENTER_YOUR_CODE_IN_THE_CONFIGURATION'); ?>
							</a>
						</td>
						<?php } ?>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<?php echo JHTML::_('form.token'); ?>
<input type="hidden" name="task" value="" />
<?php echo JHTML::_('behavior.keepalive'); ?>
</form>