<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro'); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
<div class="row-fluid">
	
	<div class="span9">
		<div class="row-fluid">
			<div class="rsepro-box">
				<div class="rsepro-box-image">
					<i class="fa fa-calendar rsepro-box-icon-color3"></i>
				</div>
				<div class="rsepro-box-content">
					<strong class="rsepro-box-number">
						<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=events', false); ?>">
							<?php echo $this->total->events; ?>
						</a>
					</strong>
					<span><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_EVENTS'); ?></span>
				</div>
			</div>
			
			<div class="rsepro-box">
				<div class="rsepro-box-image">
					<i class="fa fa-book rsepro-box-icon-color1"></i>
				</div>
				<div class="rsepro-box-content">
					<strong class="rsepro-box-number">
						<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=categories', false); ?>">
							<?php echo $this->total->categories; ?>
						</a>
					</strong>
					<span><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_CATEGORIES'); ?></span>
				</div>
			</div>
			
			<div class="rsepro-box">
				<div class="rsepro-box-image">
					<i class="fa fa-map-marker rsepro-box-icon-color2"></i>
				</div>
				<div class="rsepro-box-content">
					<strong class="rsepro-box-number">
						<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=locations', false); ?>">
							<?php echo $this->total->locations; ?>
						</a>
					</strong>
					<span><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_LOCATIONS'); ?></span>
				</div>
			</div>
			
			<div class="rsepro-box">
				<div class="rsepro-box-image">
					<i class="fa fa-user rsepro-box-icon-color4"></i>
				</div>
				<div class="rsepro-box-content">
					<strong class="rsepro-box-number">
						<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions', false); ?>">
							<?php echo $this->total->subscriptions; ?>
						</a>
					</strong>
					<span><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIPTIONS'); ?></span>
				</div>
			</div>
			
		</div>
		<div class="row-fluid">
			<?php echo JHtml::_('bootstrap.startAccordion', 'rseproDashboard', array('active' => 'upcoming', 'parent' => true)); ?>
			
			<?php if ($this->config->dashboard_upcoming) { ?>
			<?php echo JHtml::_('bootstrap.addSlide', 'rseproDashboard', JText::_('COM_RSEVENTSPRO_DASHBOARD_UPCOMING_EVENTS'), 'upcoming'); ?>
			<table class="table">
				<thead>
					<tr>
						<th><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_EVENT'); ?></th>
						<th class="center"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIBERS'); ?></th>
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
						<td class="center"><?php echo $event->subscribers; ?></td>
					</tr>
				<?php }} ?>
				</tbody>
			</table>
			<?php echo JHtml::_('bootstrap.endSlide'); ?>
			<?php } ?>
			
			<?php if ($this->config->dashboard_subscribers) { ?>
			<?php echo JHtml::_('bootstrap.addSlide', 'rseproDashboard', JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIBERS'), 'subscribers'); ?>
			<table class="table">
				<thead>
					<tr>
						<th><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_EVENT'); ?></th>
						<th class="center"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIBER_NAME'); ?></th>
						<th class="center"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIBER_DATE'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php if (!empty($this->subscribers)) { ?>
				<?php foreach ($this->subscribers as $subscriber) { ?>
					<tr>
						<td>
							<?php if ($subscriber->events) { ?>
							<?php foreach ($subscriber->events as $event) { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$event->id); ?>"><?php echo $event->name; ?></a> <br />
							<?php } ?>
							<?php } ?>
						</td>
						<td class="center"><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=subscription.edit&id='.$subscriber->id); ?>"><?php echo $subscriber->name; ?></a></td>
						<td class="center"><?php echo rseventsproHelper::showdate($subscriber->date,null,true); ?></td>
					</tr>
				<?php }} ?>
				</tbody>
			</table>
			<?php echo JHtml::_('bootstrap.endSlide'); ?>
			<?php } ?>
			
			<?php if ($this->config->dashboard_comments && !in_array($this->config->event_comment, array(0,1))) { ?>
			<?php echo JHtml::_('bootstrap.addSlide', 'rseproDashboard', JText::_('COM_RSEVENTSPRO_DASHBOARD_COMMENTS'), 'comments'); ?>
			<table class="table">
				<thead>
					<tr>
						<th><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_EVENT'); ?></th>
						<th class="center"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_COMMENT_NAME'); ?></th>
						<th class="center"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_COMMENT_DATE'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php if (!empty($this->comments)) { ?>
				<?php foreach ($this->comments as $comment) { ?>
					<tr>
						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=subscription.edit&id='.$comment->id); ?>"><?php echo $comment->name; ?></a>
						</td>
						<td class="center"><?php echo $comment->comment; ?></td>
						<td class="center">
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
			<?php echo JHtml::_('bootstrap.endSlide'); ?>
			<?php } ?>
			
			<?php echo JHtml::_('bootstrap.addSlide', 'rseproDashboard', JText::_('COM_RSEVENTSPRO_DASHBOARD_STATISTICS'), 'statistics'); ?>
			<table class="table">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th class="center"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIBERS'); ?></th>
						<th class="center"><?php echo JText::_('COM_RSEVENTSPRO_DASHBOARD_TOTAL'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php if (!empty($this->statistics)) { ?>
				<?php foreach ($this->statistics as $period => $data) { ?>
					<tr>
						<td><?php echo $period; ?></td>
						<td class="center"><?php echo $data->count; ?></td>
						<td class="center"><?php echo rseventsproHelper::currency($data->total); ?></td>
					</tr>
				<?php }} ?>
				</tbody>
			</table>
			<?php echo JHtml::_('bootstrap.endSlide'); ?>
			
			<?php echo JHtml::_('bootstrap.endAccordion'); ?>
		</div>
	</div>
	
	<div class="span3">
		<ul class="nav nav-tabs nav-stacked">
			<li class="active">
				<div class="dashboard-container">
					<div class="dashboard-info">
						<span>
							<?php echo JHtml::image('com_rseventspro/rseventspro.png', 'RSEvents!Pro', array(), true); ?>
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
			</li>
			<?php foreach ($this->buttons as $button) { ?>
			<li>
				<a href="<?php echo $button['link']; ?>">
					<i class="<?php echo $button['icon']; ?>"></i> <?php echo $button['name']; ?>
				</a>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>

<?php echo JHTML::_('form.token'); ?>
<input type="hidden" name="task" value="" />
<?php echo JHTML::_('behavior.keepalive'); ?>
</form>