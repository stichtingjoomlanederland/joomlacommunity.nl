<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<table id="rsepro-events-list" class="table table-striped adminlist">
	<thead>
		<th width="1%" align="center"><input type="checkbox" name="checkall-toggle" id="rscheckbox" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);"/></th>
		<th width="5%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('JSTATUS'); ?></th>
		<th width="5%" class="hidden-phone">&nbsp;</th>
		<th width="40%"><?php echo JText::_('COM_RSEVENTSPRO_TH_EVENT'); ?></th>
		<th width="40%" class="nowrap hidden-phone"><?php echo JText::_('COM_RSEVENTSPRO_TH_DETAILS'); ?></th>
		<th width="2%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('COM_RSEVENTSPRO_TH_HITS'); ?></th>
		<th width="1%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
	</thead>
	
	<tbody>
		<?php foreach ($this->events as $i => $id) { ?>
		<?php $row = $this->getDetails($id); ?>
		<?php $stars = rseventsproHelper::stars($row->id); ?>
		<?php $remaining = 5 - (int) $stars; ?>
		<?php $complete = empty($row->completed) ? ' rs_incomplete' : ''; ?>			
		
		<tr class="row<?php echo $i % 2; ?><?php echo $complete; ?>">
			<td align="center" class="center"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
			<td align="center" class="center hidden-phone">
				<div class="btn-group">
					<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
					<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
					<a class="btn btn-micro hasTooltip" title="<?php echo JText::_('COM_RSEVENTSPRO_PREVIEW_EVENT'); ?>" target="_blank" href="<?php echo JUri::root(); ?>index.php?option=com_rseventspro&layout=show&id=<?php echo $row->id; ?>"><span class="icon-zoom-in"></span></a>
				</div>
			</td>
			<td class="hidden-phone center">
				<div class="rs_event_img">
					<img src="<?php echo rseventsproHelper::thumb($row->id, 70); ?>" alt="" width="70" />
				</div>
			</td>
			<td class="has-context">
				<?php if ($stars) { ?>
				<div class="rs_stars">
					<?php for ($i=0;$i<$stars;$i++) { ?>
					<i class="fa fa-star" style="color: #e3cf7a;"></i>
					<?php } ?>
					<?php for ($i=0;$i<$remaining;$i++) { ?>
					<i class="fa fa-star-o"></i>
					<?php } ?>
				</div>
				<?php } ?>
				<div class="rs_event_details">
					<p>
						<?php if ($row->parent) { ?><i class="fa fa-child" title="<?php echo ucfirst(JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO')); ?>"></i><?php } ?>
						<b><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$row->id); ?>"><?php echo $row->name; ?></a></b>
						<?php if (empty($row->completed)) echo '<small class="muted">'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</small>'; ?>
						<?php echo rseventsproHelper::report($row->id); ?>
					</p>
					
					<p>
						<small class="muted">
							<?php echo $row->allday ? rseventsproHelper::showdate($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::showdate($row->start,null,true).' - '.rseventsproHelper::showdate($row->end,null,true); ?>
						</small>
					</p>
					
					<?php $availabletickets = $row->registration ? $this->getTickets($row->id) : false; ?>
					<?php $subscriptions = $row->registration ? $this->getSubscribers($row->id) : false; ?>
					<?php $waitinglist = $row->registration ? $this->getWaitingList($row->id) : false; ?>
					
					<?php if ($availabletickets) { ?><p><small class="muted"><?php echo $availabletickets; ?></small></p><?php } ?>
					
					<p>
						<?php if ($subscriptions) { ?>
						<a class="btn btn-small" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a>
						<?php } ?>
						
						<?php if ($waitinglist) { ?>
						<a class="btn btn-small" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=waitinglist&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_WAITINGLIST'); ?></a>
						<?php } ?>
						
						<?php if ($row->rsvp) { ?>
						<a class="btn btn-small" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=rsvp&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_RSVP_GUESTS'); ?></a>
						<?php } ?>						
					</p>
				</div>
			</td>
			<td class="hidden-phone">
				<?php $categories = rseventsproHelper::categories($row->id, true, ', '); ?>
				<?php $tags = rseventsproHelper::tags($row->id, true); ?>
				<p><i class="fa fa-user fa-fw"></i> <?php echo empty($row->owner) ? JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST') : $row->uname; ?></p>
				<?php if ($row->lid) { ?><p><i class="fa fa-map-marker fa-fw"></i> <a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=location.edit&id='.$row->lid); ?>"><?php echo $row->lname; ?></a></p><?php } ?>
				<?php if ($categories) { ?><p><i class="fa fa-book fa-fw"></i> <?php echo $categories; ?></p><?php } ?>
				<?php if ($tags) { ?><p><i class="fa fa-tags fa-fw"></i> <?php echo $tags; ?></p><?php } ?>
			</td>
			<td align="center" class="center hidden-phone"><?php echo $row->hits; ?></td>
			<td class="center hidden-phone"><?php echo $id; ?></td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="7" style="text-align:center;">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
</table>