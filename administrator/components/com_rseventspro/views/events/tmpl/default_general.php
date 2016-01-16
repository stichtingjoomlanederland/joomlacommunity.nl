<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<table class="table table-striped adminlist">
	<thead>
		<th width="1%" align="center"><input type="checkbox" name="checkall-toggle" id="rscheckbox" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);"/></th>
		<th width="5%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('JSTATUS'); ?></th>
		<th class="nowrap hidden-phone">&nbsp;</th>
		<th width="40%"><?php echo JText::_('COM_RSEVENTSPRO_TH_EVENT'); ?></th>
		<th width="10%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('COM_RSEVENTSPRO_TH_LOCATION'); ?></th>
		<th width="10%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('COM_RSEVENTSPRO_TH_OWNER'); ?></th>
		<th width="10%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('COM_RSEVENTSPRO_TH_CATEGORIES'); ?></th>
		<th width="10%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('COM_RSEVENTSPRO_TH_TAGS'); ?></th>
		<th width="10%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('COM_RSEVENTSPRO_TH_ENDING'); ?></th>
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
			<td align="center" class="center" style="vertical-align:middle;"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
			<td align="center" class="center hidden-phone" style="vertical-align:middle;">
				<div class="btn-group">
					<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
					<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
				</div>
			</td>
			<td class="hidden-phone">
				<div class="rs_event_img">
					<img src="<?php echo JURI::root().'index.php?option=com_rseventspro&task=image&id='.$row->id.'&width=70'; ?>" alt="" width="70" />
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
						<?php if ($row->parent) { ?>
						<i class="fa fa-child" title="<?php echo ucfirst(JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO')); ?>"></i>
						<?php } ?>
						
						<b><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$row->id); ?>"><?php echo $row->name; ?></a></b>
						<?php if (empty($row->completed)) echo '<b>'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</b>'; ?>
						<?php echo rseventsproHelper::report($row->id); ?>
					</p>
					<p><?php echo $row->allday ? rseventsproHelper::showdate($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::showdate($row->start,null,true); ?></p>
					<?php if ($availabletickets = $this->getTickets($row->id)) { ?>
					<p><?php echo $availabletickets; ?></p>
					<?php } ?>
					<?php if ($subscriptions = $this->getSubscribers($row->id)) { ?>
					<p><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a></p>
					<?php } ?>
				</div>
			</td>
			<td align="center" class="center hidden-phone"><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=location.edit&id='.$row->lid); ?>"><?php echo $row->lname; ?></a></td>
			<td align="center" class="center hidden-phone"><?php echo empty($row->owner) ? JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST') : $row->uname; ?></td>
			<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::categories($row->id, true); ?></td>
			<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::tags($row->id,true); ?></td>
			<td align="center" class="center hidden-phone"><?php echo $row->allday ? '' : rseventsproHelper::showdate($row->end,null,true); ?></td>
			<td align="center" class="center hidden-phone"><?php echo $row->hits; ?></td>
			<td class="center hidden-phone"><?php echo $id; ?></td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="11">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
</table>