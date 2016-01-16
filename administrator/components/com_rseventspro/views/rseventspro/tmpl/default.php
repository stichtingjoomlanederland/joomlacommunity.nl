<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro'); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
<div class="row-fluid">
	<div class="width-<?php echo $this->middle ? 30 : 70; ?> fltlft">
		<div class="dashboard-container">
			<?php foreach ($this->buttons as $button) { ?>
			<div class="rsspan2">
				<div class="dashboard-wraper">
					<div class="dashboard-content"> 
						<a href="<?php echo $button['link']; ?>"> 
							<i class="<?php echo $button['icon']; ?>"></i>
							<span class="dashboard-title"><?php echo $button['name']; ?></span> 
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
									(<?php echo rseventsproHelper::showdate($event->start,null,true); ?><?php if (!$event->allday) { ?> - <?php echo rseventsproHelper::showdate($event->end,null,true); } ?>)
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
								<td align="center"><?php echo rseventsproHelper::showdate($subscriber->date,null,true); ?></td>
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
								<td align="center">
									<?php 
										if (strlen((int) $comment->date) == 10) {
											$comment->date = @date('Y-m-d H:i:s',$comment->date);
										}
										echo rseventsproHelper::showdate($comment->date,null,true);
									?>
								</td>
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
						<td nowrap="nowrap"><b>RSEvents!Pro <?php echo $this->version; ?></b></td>
					</tr>
					<tr>
						<td nowrap="nowrap"><strong><?php echo JText::_('COM_RSEVENTSPRO_COPYRIGHT_NAME') ?>: </strong></td>
						<td nowrap="nowrap">&copy; 2007 - <?php echo gmdate('Y'); ?> <a href="http://www.rsjoomla.com" target="_blank">RSJoomla.com</a></td>
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