<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php $i = 0; ?>
<?php $cols = 7; ?>
<table id="rsepro-events-list" class="table table-striped">
	<thead>
		<th width="1%" class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('grid.checkall'); ?></th>
		<th width="10%" class="nowrap hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JText::_('JSTATUS'); ?></th>
		<th width="5%" class="hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">&nbsp;</th>
		<th width="40%"><?php echo JText::_('COM_RSEVENTSPRO_TH_EVENT'); ?></th>
		<th width="40%" class="nowrap hidden-phone"><?php echo JText::_('COM_RSEVENTSPRO_TH_DETAILS'); ?></th>
		<th width="2%" class="nowrap hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_TH_HITS'); ?></th>
		<th width="1%" class="nowrap hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
	</thead>
	
	<?php if (!empty($this->ongoing)) { ?>
	<tbody id="rseprocontainer_ongoing">
		<tr>
			<td colspan="<?php echo $cols; ?>" class="rsepro_header"><?php echo JText::_('COM_RSEVENTSPRO_TD_ONGOING_EVENTS'); ?></td>
		</tr>
		<?php $k = 0; ?>
		<?php $n = count($this->ongoing); ?>
		<?php foreach ($this->ongoing as $id) { ?>
		<?php $row = $this->getDetails($id); ?>
		<?php $stars = rseventsproHelper::stars($row->id); ?>
		<?php $remaining = 5 - (int) $stars; ?>
		<?php $complete = empty($row->completed) ? ' rs_incomplete' : ''; ?>			
		
		<tr class="<?php echo 'row'.$k.$complete; ?>">
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone">
				<?php if (!rseventsproHelper::isJ4()) { ?><div class="btn-group"><?php } ?>
					<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
					<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
					<?php echo JHtml::_('rseventspro.preview', $row->id); ?>
				<?php if (!rseventsproHelper::isJ4()) { ?></div><?php } ?>
			</td>
			<td class="hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
				<div class="rs_event_img">
					<img src="<?php echo rseventsproHelper::thumb($row->id, 70).'?nocache='.uniqid(''); ?>" alt="" width="70" />
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
						<?php if (empty($row->completed)) echo '<small class="'.RSEventsproAdapterGrid::styles(array('muted')).'">'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</small>'; ?>
						<?php if ($row->published == 3) echo '<small class="'.RSEventsproAdapterGrid::styles(array('muted')).' text-error">'.JText::_('COM_RSEVENTSPRO_GLOBAL_CANCELED_EVENT').'</small>'; ?>
						<?php echo rseventsproHelper::report($row->id); ?>
					</p>
					
					<p>
						<small class="<?php echo RSEventsproAdapterGrid::styles(array('muted')); ?>">
							<?php echo $row->allday ? rseventsproHelper::showdate($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::showdate($row->start,null,true).' - '.rseventsproHelper::showdate($row->end,null,true); ?>
						</small>
					</p>
					
					<?php $availabletickets = $row->registration ? $this->getTickets($row->id) : false; ?>
					<?php $subscriptions = $row->registration ? $this->getSubscribers($row->id) : false; ?>
					<?php $waitinglist = $row->registration ? $this->getWaitingList($row->id) : false; ?>
					<?php $unsubscribers = $row->registration ? $this->getUnsubscribers($row->id) : false; ?>
					
					<?php if ($availabletickets) { ?><p><small class="<?php echo RSEventsproAdapterGrid::styles(array('muted')); ?>"><?php echo $availabletickets; ?></small></p><?php } ?>
					
					<p>
						<?php if ($subscriptions) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a>
						<?php } ?>
						
						<?php if ($waitinglist) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=waitinglist&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_WAITINGLIST'); ?></a>
						<?php } ?>
						
						<?php if ($row->rsvp) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=rsvp&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_RSVP_GUESTS'); ?></a>
						<?php } ?>
						
						<?php if ($unsubscribers) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=unsubscribers&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBERS'); ?></a>
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
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone"><?php echo $row->hits; ?></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone"><?php echo $id; ?></td>
		</tr>
		<?php $i++; ?>
		<?php $k = 1-$k; ?>
		<?php } ?>
	</tbody>
	<?php if ($this->total_ongoing > $n) { ?>
	<tbody id="ongoing">
		<tr>
			<td colspan="<?php echo $cols; ?>" style="text-align:center;">
				<button type="button" class="rsepromore_inactive" id="rsepro_loadmore_ongoing"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_MORE_RESULTS'); ?></button>
			</td>
		</tr>
	</tbody>
	<?php } ?>
	<?php } ?>
	
	<?php if (!empty($this->thisweek)) { ?>
	<tbody id="rseprocontainer_thisweek">
		<tr>
			<td colspan="<?php echo $cols; ?>" class="rsepro_header"><?php echo JText::_('COM_RSEVENTSPRO_TD_THISWEEK_EVENTS'); ?></td>
		</tr>
		<?php $k = 0; ?>
		<?php $n = count($this->thisweek); ?>
		<?php foreach ($this->thisweek as $id) { ?>
		<?php $row = $this->getDetails($id); ?>
		<?php $stars = rseventsproHelper::stars($row->id); ?>
		<?php $remaining = 5 - (int) $stars; ?>
		<?php $complete = empty($row->completed) ? ' rs_incomplete' : ''; ?>			
		
		<tr class="<?php echo 'row'.$k.$complete; ?>">
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone">
				<?php if (!rseventsproHelper::isJ4()) { ?><div class="btn-group"><?php } ?>
					<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
					<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
					<?php echo JHtml::_('rseventspro.preview', $row->id); ?>
				<?php if (!rseventsproHelper::isJ4()) { ?></div><?php } ?>
			</td>
			<td class="hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
				<div class="rs_event_img">
					<img src="<?php echo rseventsproHelper::thumb($row->id, 70).'?nocache='.uniqid(''); ?>" alt="" width="70" />
				</div>
			</td>
			<td class="nowrap has-context">
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
						<?php if (empty($row->completed)) echo '<small class="'.RSEventsproAdapterGrid::styles(array('muted')).'">'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</small>'; ?>
						<?php if ($row->published == 3) echo '<small class="'.RSEventsproAdapterGrid::styles(array('muted')).' text-error">'.JText::_('COM_RSEVENTSPRO_GLOBAL_CANCELED_EVENT').'</small>'; ?>
						<?php echo rseventsproHelper::report($row->id); ?>
					</p>
					
					<p>
						<small class="<?php echo RSEventsproAdapterGrid::styles(array('muted')); ?>">
							<?php echo $row->allday ? rseventsproHelper::showdate($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::showdate($row->start,null,true).' - '.rseventsproHelper::showdate($row->end,null,true); ?>
						</small>
					</p>
					
					<?php $availabletickets = $row->registration ? $this->getTickets($row->id) : false; ?>
					<?php $subscriptions = $row->registration ? $this->getSubscribers($row->id) : false; ?>
					<?php $waitinglist = $row->registration ? $this->getWaitingList($row->id) : false; ?>
					<?php $unsubscribers = $row->registration ? $this->getUnsubscribers($row->id) : false; ?>
					
					<?php if ($availabletickets) { ?><p><small class="<?php echo RSEventsproAdapterGrid::styles(array('muted')); ?>"><?php echo $availabletickets; ?></small></p><?php } ?>
					
					<p>
						<?php if ($subscriptions) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a>
						<?php } ?>
						
						<?php if ($waitinglist) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=waitinglist&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_WAITINGLIST'); ?></a>
						<?php } ?>
						
						<?php if ($row->rsvp) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=rsvp&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_RSVP_GUESTS'); ?></a>
						<?php } ?>
						
						<?php if ($unsubscribers) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=unsubscribers&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBERS'); ?></a>
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
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone"><?php echo $row->hits; ?></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone"><?php echo $id; ?></td>
		</tr>
		<?php $i++; ?>
		<?php $k = 1-$k; ?>
		<?php } ?>
	</tbody>
	<?php if ($this->total_thisweek > $n) { ?>
	<tbody id="thisweek">
		<tr>
			<td colspan="<?php echo $cols; ?>" style="text-align:center;">
				<button type="button" class="rsepromore_inactive" id="rsepro_loadmore_thisweek"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_MORE_RESULTS'); ?></button>
			</td>
		</tr>
	</tbody>
	<?php } ?>
	<?php } ?>
	
	<?php if (!empty($this->thismonth)) { ?>
	<tbody id="rseprocontainer_thismonth">
		<tr>
			<td colspan="<?php echo $cols; ?>" class="rsepro_header"><?php echo JText::_('COM_RSEVENTSPRO_TD_THISMONTH_EVENTS'); ?></td>
		</tr>
		<?php $k = 0; ?>
		<?php $n = count($this->thismonth); ?>
		<?php foreach ($this->thismonth as $id) { ?>
		<?php $row = $this->getDetails($id); ?>
		<?php $stars = rseventsproHelper::stars($row->id); ?>
		<?php $remaining = 5 - (int) $stars; ?>
		<?php $complete = empty($row->completed) ? ' rs_incomplete' : ''; ?>			
		
		<tr class="<?php echo 'row'.$k.$complete; ?>">
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone">
				<?php if (!rseventsproHelper::isJ4()) { ?><div class="btn-group"><?php } ?>
					<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
					<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
					<?php echo JHtml::_('rseventspro.preview', $row->id); ?>
				<?php if (!rseventsproHelper::isJ4()) { ?></div><?php } ?>
			</td>
			<td class="hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
				<div class="rs_event_img">
					<img src="<?php echo rseventsproHelper::thumb($row->id, 70).'?nocache='.uniqid(''); ?>" alt="" width="70" />
				</div>
			</td>
			<td class="nowrap has-context">
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
						<?php if (empty($row->completed)) echo '<small class="'.RSEventsproAdapterGrid::styles(array('muted')).'">'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</small>'; ?>
						<?php if ($row->published == 3) echo '<small class="'.RSEventsproAdapterGrid::styles(array('muted')).' text-error">'.JText::_('COM_RSEVENTSPRO_GLOBAL_CANCELED_EVENT').'</small>'; ?>
						<?php echo rseventsproHelper::report($row->id); ?>
					</p>
					
					<p>
						<small class="<?php echo RSEventsproAdapterGrid::styles(array('muted')); ?>">
							<?php echo $row->allday ? rseventsproHelper::showdate($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::showdate($row->start,null,true).' - '.rseventsproHelper::showdate($row->end,null,true); ?>
						</small>
					</p>
					
					<?php $availabletickets = $row->registration ? $this->getTickets($row->id) : false; ?>
					<?php $subscriptions = $row->registration ? $this->getSubscribers($row->id) : false; ?>
					<?php $waitinglist = $row->registration ? $this->getWaitingList($row->id) : false; ?>
					<?php $unsubscribers = $row->registration ? $this->getUnsubscribers($row->id) : false; ?>
					
					<?php if ($availabletickets) { ?><p><small class="<?php echo RSEventsproAdapterGrid::styles(array('muted')); ?>"><?php echo $availabletickets; ?></small></p><?php } ?>
					
					<p>
						<?php if ($subscriptions) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a>
						<?php } ?>
						
						<?php if ($waitinglist) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=waitinglist&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_WAITINGLIST'); ?></a>
						<?php } ?>
						
						<?php if ($row->rsvp) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=rsvp&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_RSVP_GUESTS'); ?></a>
						<?php } ?>
						
						<?php if ($unsubscribers) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=unsubscribers&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBERS'); ?></a>
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
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone"><?php echo $row->hits; ?></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone"><?php echo $id; ?></td>
		</tr>
		<?php $i++; ?>
		<?php $k = 1-$k; ?>
		<?php } ?>
	</tbody>
	<?php if ($this->total_thismonth > $n) { ?>
	<tbody id="thismonth">
		<tr>
			<td colspan="<?php echo $cols; ?>" style="text-align:center;">
				<button type="button" class="rsepromore_inactive" id="rsepro_loadmore_thismonth"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_MORE_RESULTS'); ?></button>
			</td>
		</tr>
	</tbody>
	<?php } ?>
	<?php } ?>
	
	<?php if (!empty($this->nextmonth)) { ?>
	<tbody id="rseprocontainer_nextmonth">
		<tr>
			<td colspan="<?php echo $cols; ?>" class="rsepro_header"><?php echo JText::_('COM_RSEVENTSPRO_TD_NEXTMONTH_EVENTS'); ?></td>
		</tr>
		<?php $k = 0; ?>
		<?php $n = count($this->nextmonth); ?>
		<?php foreach ($this->nextmonth as $id) { ?>
		<?php $row = $this->getDetails($id); ?>
		<?php $stars = rseventsproHelper::stars($row->id); ?>
		<?php $remaining = 5 - (int) $stars; ?>
		<?php $complete = empty($row->completed) ? ' rs_incomplete' : ''; ?>			
		
		<tr class="<?php echo 'row'.$k.$complete; ?>">
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone">
				<?php if (!rseventsproHelper::isJ4()) { ?><div class="btn-group"><?php } ?>
					<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
					<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
					<?php echo JHtml::_('rseventspro.preview', $row->id); ?>
				<?php if (!rseventsproHelper::isJ4()) { ?></div><?php } ?>
			</td>
			<td class="hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
				<div class="rs_event_img">
					<img src="<?php echo rseventsproHelper::thumb($row->id, 70).'?nocache='.uniqid(''); ?>" alt="" width="70" />
				</div>
			</td>
			<td class="nowrap has-context">
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
						<?php if (empty($row->completed)) echo '<small class="'.RSEventsproAdapterGrid::styles(array('muted')).'">'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</small>'; ?>
						<?php if ($row->published == 3) echo '<small class="'.RSEventsproAdapterGrid::styles(array('muted')).' text-error">'.JText::_('COM_RSEVENTSPRO_GLOBAL_CANCELED_EVENT').'</small>'; ?>
						<?php echo rseventsproHelper::report($row->id); ?>
					</p>
					
					<p>
						<small class="<?php echo RSEventsproAdapterGrid::styles(array('muted')); ?>">
							<?php echo $row->allday ? rseventsproHelper::showdate($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::showdate($row->start,null,true).' - '.rseventsproHelper::showdate($row->end,null,true); ?>
						</small>
					</p>
					
					<?php $availabletickets = $row->registration ? $this->getTickets($row->id) : false; ?>
					<?php $subscriptions = $row->registration ? $this->getSubscribers($row->id) : false; ?>
					<?php $waitinglist = $row->registration ? $this->getWaitingList($row->id) : false; ?>
					<?php $unsubscribers = $row->registration ? $this->getUnsubscribers($row->id) : false; ?>
					
					<?php if ($availabletickets) { ?><p><small class="<?php echo RSEventsproAdapterGrid::styles(array('muted')); ?>"><?php echo $availabletickets; ?></small></p><?php } ?>
					
					<p>
						<?php if ($subscriptions) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a>
						<?php } ?>
						
						<?php if ($waitinglist) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=waitinglist&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_WAITINGLIST'); ?></a>
						<?php } ?>
						
						<?php if ($row->rsvp) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=rsvp&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_RSVP_GUESTS'); ?></a>
						<?php } ?>
						
						<?php if ($unsubscribers) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=unsubscribers&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBERS'); ?></a>
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
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone"><?php echo $row->hits; ?></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone"><?php echo $id; ?></td>
		</tr>
		<?php $i++; ?>
		<?php $k = 1-$k; ?>
		<?php } ?>
	</tbody>
	<?php if ($this->total_nextmonth > $n) { ?>
	<tbody id="nextmonth">
		<tr>
			<td colspan="<?php echo $cols; ?>" style="text-align:center;">
				<button type="button" class="rsepromore_inactive" id="rsepro_loadmore_nextmonth"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_MORE_RESULTS'); ?></button>
			</td>
		</tr>
	</tbody>
	<?php } ?>
	<?php } ?>
	
	<?php if (!empty($this->upcoming)) { ?>
	<tbody id="rseprocontainer_upcoming">
		<tr>
			<td colspan="<?php echo $cols; ?>" class="rsepro_header"><?php echo JText::_('COM_RSEVENTSPRO_TD_UPCOMING_EVENTS'); ?></td>
		</tr>
		<?php $k = 0; ?>
		<?php $n = count($this->upcoming); ?>
		<?php foreach ($this->upcoming as $id) { ?>
		<?php $row = $this->getDetails($id); ?>
		<?php $stars = rseventsproHelper::stars($row->id); ?>
		<?php $remaining = 5 - (int) $stars; ?>
		<?php $complete = empty($row->completed) ? ' rs_incomplete' : ''; ?>			
		
		<tr class="<?php echo 'row'.$k.$complete; ?>">
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone">
				<?php if (!rseventsproHelper::isJ4()) { ?><div class="btn-group"><?php } ?>
					<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
					<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
					<?php echo JHtml::_('rseventspro.preview', $row->id); ?>
				<?php if (!rseventsproHelper::isJ4()) { ?></div><?php } ?>
			</td>
			<td class="hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
				<div class="rs_event_img">
					<img src="<?php echo rseventsproHelper::thumb($row->id, 70).'?nocache='.uniqid(''); ?>" alt="" width="70" />
				</div>
			</td>
			<td class="nowrap has-context">
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
						<?php if (empty($row->completed)) echo '<small class="'.RSEventsproAdapterGrid::styles(array('muted')).'">'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</small>'; ?>
						<?php if ($row->published == 3) echo '<small class="'.RSEventsproAdapterGrid::styles(array('muted')).' text-error">'.JText::_('COM_RSEVENTSPRO_GLOBAL_CANCELED_EVENT').'</small>'; ?>
						<?php echo rseventsproHelper::report($row->id); ?>
					</p>
					
					<p>
						<small class="<?php echo RSEventsproAdapterGrid::styles(array('muted')); ?>">
							<?php echo $row->allday ? rseventsproHelper::showdate($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::showdate($row->start,null,true).' - '.rseventsproHelper::showdate($row->end,null,true); ?>
						</small>
					</p>
					
					<?php $availabletickets = $row->registration ? $this->getTickets($row->id) : false; ?>
					<?php $subscriptions = $row->registration ? $this->getSubscribers($row->id) : false; ?>
					<?php $waitinglist = $row->registration ? $this->getWaitingList($row->id) : false; ?>
					<?php $unsubscribers = $row->registration ? $this->getUnsubscribers($row->id) : false; ?>
					
					<?php if ($availabletickets) { ?><p><small class="<?php echo RSEventsproAdapterGrid::styles(array('muted')); ?>"><?php echo $availabletickets; ?></small></p><?php } ?>
					
					<p>
						<?php if ($subscriptions) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a>
						<?php } ?>
						
						<?php if ($waitinglist) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=waitinglist&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_WAITINGLIST'); ?></a>
						<?php } ?>
						
						<?php if ($row->rsvp) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=rsvp&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_RSVP_GUESTS'); ?></a>
						<?php } ?>
						
						<?php if ($unsubscribers) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=unsubscribers&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBERS'); ?></a>
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
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone"><?php echo $row->hits; ?></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone"><?php echo $id; ?></td>
		</tr>
		<?php $i++; ?>
		<?php $k = 1-$k; ?>
		<?php } ?>
	</tbody>
	<?php if ($this->total_upcoming > $n) { ?>
	<tbody id="upcoming">
		<tr>
			<td colspan="<?php echo $cols; ?>" style="text-align:center;">
				<button type="button" class="rsepromore_inactive" id="rsepro_loadmore_upcoming"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_MORE_RESULTS'); ?></button>
			</td>
		</tr>
	</tbody>
	<?php } ?>
	<?php } ?>
	
	<?php if (!empty($this->past)) { ?>
	<tbody id="rseprocontainer_past">
		<tr>
			<td colspan="<?php echo $cols; ?>" class="rsepro_header"><?php echo JText::_('COM_RSEVENTSPRO_TD_PAST_EVENTS'); ?></td>
		</tr>
		<?php $k = 0; ?>
		<?php $n = count($this->past); ?>
		<?php foreach ($this->past as $id) { ?>
		<?php $row = $this->getDetails($id); ?>
		<?php $stars = rseventsproHelper::stars($row->id); ?>
		<?php $remaining = 5 - (int) $stars; ?>
		<?php $complete = empty($row->completed) ? ' rs_incomplete' : ''; ?>			
		
		<tr class="<?php echo 'row'.$k.$complete; ?>">
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone">
				<?php if (!rseventsproHelper::isJ4()) { ?><div class="btn-group"><?php } ?>
					<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
					<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
					<?php echo JHtml::_('rseventspro.preview', $row->id); ?>
				<?php if (!rseventsproHelper::isJ4()) { ?></div><?php } ?>
			</td>
			<td class="hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
				<div class="rs_event_img">
					<img src="<?php echo rseventsproHelper::thumb($row->id, 70).'?nocache='.uniqid(''); ?>" alt="" width="70" />
				</div>
			</td>
			<td class="nowrap has-context">
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
						<?php if (empty($row->completed)) echo '<small class="'.RSEventsproAdapterGrid::styles(array('muted')).'">'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</small>'; ?>
						<?php if ($row->published == 3) echo '<small class="'.RSEventsproAdapterGrid::styles(array('muted')).' text-error">'.JText::_('COM_RSEVENTSPRO_GLOBAL_CANCELED_EVENT').'</small>'; ?>
						<?php echo rseventsproHelper::report($row->id); ?>
					</p>
					
					<p>
						<small class="<?php echo RSEventsproAdapterGrid::styles(array('muted')); ?>">
							<?php echo $row->allday ? rseventsproHelper::showdate($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::showdate($row->start,null,true).' - '.rseventsproHelper::showdate($row->end,null,true); ?>
						</small>
					</p>
					
					<?php $availabletickets = $row->registration ? $this->getTickets($row->id) : false; ?>
					<?php $subscriptions = $row->registration ? $this->getSubscribers($row->id) : false; ?>
					<?php $waitinglist = $row->registration ? $this->getWaitingList($row->id) : false; ?>
					<?php $unsubscribers = $row->registration ? $this->getUnsubscribers($row->id) : false; ?>
					
					<?php if ($availabletickets) { ?><p><small class="<?php echo RSEventsproAdapterGrid::styles(array('muted')); ?>"><?php echo $availabletickets; ?></small></p><?php } ?>
					
					<p>
						<?php if ($subscriptions) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a>
						<?php } ?>
						
						<?php if ($waitinglist) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=waitinglist&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_WAITINGLIST'); ?></a>
						<?php } ?>
						
						<?php if ($row->rsvp) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=rsvp&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_RSVP_GUESTS'); ?></a>
						<?php } ?>
						
						<?php if ($unsubscribers) { ?>
						<a class="<?php echo RSEventsproAdapterGrid::styles(array('btn', 'btn-small')); ?>" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=unsubscribers&id='.$row->id); ?>"><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBERS'); ?></a>
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
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone"><?php echo $row->hits; ?></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone"><?php echo $id; ?></td>
		</tr>
		<?php $i++; ?>
		<?php $k = 1-$k; ?>
		<?php } ?>
	</tbody>
	<?php if ($this->total_past > $n) { ?>
	<tbody id="past">
		<tr>
			<td colspan="<?php echo $cols; ?>" style="text-align:center;">
				<button type="button" class="rsepromore_inactive" id="rsepro_loadmore_past"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_MORE_RESULTS'); ?></button>
			</td>
		</tr>
	</tbody>
	<?php } ?>
	<?php } ?>
</table>