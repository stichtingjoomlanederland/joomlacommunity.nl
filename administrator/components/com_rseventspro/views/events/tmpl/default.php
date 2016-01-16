<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.tooltip');
JHtml::_('behavior.keepalive');
?>

<script type="text/javascript">
	window.addEvent('domready', function(){
	<?php if ($this->total_past > count($this->past)) { ?>
	$('rsepro_loadmore_past').addEvent('click', function(el) {
		var lstart_past = $('rseprocontainer_past').getElements('tr');
		rspagination('events',lstart_past.length,'past');
	});
	<?php } ?>
	<?php if ($this->total_ongoing > count($this->ongoing)) { ?>
	$('rsepro_loadmore_ongoing').addEvent('click', function(el) {
		var lstart_ongoing = $('rseprocontainer_ongoing').getElements('tr');
		rspagination('events',lstart_ongoing.length - 1,'ongoing');
	});
	<?php } ?>
	<?php if ($this->total_thisweek > count($this->thisweek)) { ?>
	$('rsepro_loadmore_thisweek').addEvent('click', function(el) {
		var lstart_thisweek = $('rseprocontainer_thisweek').getElements('tr');
		rspagination('events',lstart_thisweek.length - 1,'thisweek');
	});
	<?php } ?>
	<?php if ($this->total_thismonth > count($this->thismonth)) { ?>
	$('rsepro_loadmore_thismonth').addEvent('click', function(el) {
		var lstart_thismonth = $('rseprocontainer_thismonth').getElements('tr');
		rspagination('events',lstart_thismonth.length - 1,'thismonth');
	});
	<?php } ?>
	<?php if ($this->total_nextmonth > count($this->nextmonth)) { ?>
	$('rsepro_loadmore_nextmonth').addEvent('click', function(el) {
		var lstart_nextmonth = $('rseprocontainer_nextmonth').getElements('tr');
		rspagination('events',lstart_nextmonth.length - 1,'nextmonth');
	});
	<?php } ?>
	<?php if ($this->total_upcoming > count($this->upcoming)) { ?>
	$('rsepro_loadmore_upcoming').addEvent('click', function(el) {
		var lstart_upcoming = $('rseprocontainer_upcoming').getElements('tr');
		rspagination('events',lstart_upcoming.length - 1,'upcoming');
	});	
	<?php } ?>
	
	new elSelect({container : 'rs_select_top1', 
		onselect : function (el) {
			if (el.selected.getProperty('value') == 'archived') {
				$('rs_select_top2').setStyle('display','none');
				$('search').setStyle('display','none');
				$('rs_select_top5').setStyle('display','');
				$('rs_select_top6').setStyle('display','none');
				$('rs_select_top7').setStyle('display','none');
				$('rs_select_top8').setStyle('display','none');
				$('rs_select_top9').setStyle('display','none');
				
				$('filter_archived').disabled = false;
				$('filter_featured').disabled = true;
				$('filter_child').disabled = true;
				$('filter_start').disabled = true;
				$('filter_end').disabled = true;
				
				
			} else if (el.selected.getProperty('value') == 'start') {
				$('rs_select_top2').setStyle('display','none');
				$('search').setStyle('display','none');
				$('rs_select_top5').setStyle('display','none');
				$('rs_select_top6').setStyle('display','');
				$('rs_select_top7').setStyle('display','none');
				$('rs_select_top8').setStyle('display','none');
				$('rs_select_top9').setStyle('display','none');
				
				$('filter_archived').disabled = true;
				$('filter_featured').disabled = true;
				$('filter_child').disabled = true;
				$('filter_start').disabled = false;
				$('filter_end').disabled = true;
				
			} else if (el.selected.getProperty('value') == 'end') {
				$('rs_select_top2').setStyle('display','none');
				$('search').setStyle('display','none');
				$('rs_select_top5').setStyle('display','none');
				$('rs_select_top6').setStyle('display','none');
				$('rs_select_top7').setStyle('display','');
				$('rs_select_top8').setStyle('display','none');
				$('rs_select_top9').setStyle('display','none');
				
				$('filter_archived').disabled = true;
				$('filter_featured').disabled = true;
				$('filter_child').disabled = true;
				$('filter_start').disabled = true;
				$('filter_end').disabled = false;
				
			} else if (el.selected.getProperty('value') == 'child') {
				$('rs_select_top2').setStyle('display','none');
				$('search').setStyle('display','none');
				$('rs_select_top5').setStyle('display','none');
				$('rs_select_top6').setStyle('display','none');
				$('rs_select_top7').setStyle('display','none');
				$('rs_select_top8').setStyle('display','');
				$('rs_select_top9').setStyle('display','none');
				
				$('filter_archived').disabled = true;
				$('filter_featured').disabled = true;
				$('filter_child').disabled = false;
				$('filter_start').disabled = true;
				$('filter_end').disabled = true;
				
			} else if (el.selected.getProperty('value') == 'featured') {
				$('rs_select_top2').setStyle('display','none');
				$('search').setStyle('display','none');
				$('rs_select_top5').setStyle('display','none');
				$('rs_select_top6').setStyle('display','none');
				$('rs_select_top7').setStyle('display','none');
				$('rs_select_top8').setStyle('display','none');
				$('rs_select_top9').setStyle('display','');
				
				$('filter_archived').disabled = true;
				$('filter_featured').disabled = false;
				$('filter_child').disabled = true;
				$('filter_start').disabled = true;
				$('filter_end').disabled = true;
				
			} else {
				$('rs_select_top2').setStyle('display','');
				$('search').setStyle('display','');
				$('rs_select_top5').setStyle('display','none');
				$('rs_select_top6').setStyle('display','none');
				$('rs_select_top7').setStyle('display','none');
				$('rs_select_top8').setStyle('display','none');
				$('rs_select_top9').setStyle('display','none');
				
				$('filter_archived').disabled = true;
				$('filter_featured').disabled = true;
				$('filter_child').disabled = true;
				$('filter_start').disabled = true;
				$('filter_end').disabled = true;
			}
		} 
	});
	new elSelect( {container : 'rs_select_top2'} );
	new elSelect( {container : 'rs_select_top3', onselect : function (el) { 
		$('filter_archived').disabled = true;
		$('filter_featured').disabled = true;
		$('filter_child').disabled = true;
		$('filter_start').disabled = true;
		$('filter_end').disabled = true;
		document.adminForm.submit(); 
	} } );
	new elSelect( {container : 'rs_select_top4', onselect : function (el) { 
		$('filter_archived').disabled = true;
		$('filter_featured').disabled = true;
		$('filter_child').disabled = true;
		$('filter_start').disabled = true;
		$('filter_end').disabled = true;
		document.adminForm.submit(); 
	} } );
	new elSelect( {container : 'rs_select_top5'} );
	new elSelect( {container : 'rs_select_top8'} );
	new elSelect( {container : 'rs_select_top9'} );
});

function rs_add_option(theoption) {
	$('search').value = theoption;
	$('rs_results').style.display = 'none';
}

function rs_add_filter() {
	if ($('filter_from').value == 'events' || $('filter_from').value == 'description' || $('filter_from').value == 'locations' || $('filter_from').value == 'categories' || $('filter_from').value == 'tags') {
		$('filter_archived').disabled = true;
		$('filter_featured').disabled = true;
		$('filter_child').disabled = true;
		$('filter_start').disabled = true;
		$('filter_end').disabled = true;
	}
	
	if ($('search').value != '' || $('filter_from').value == 'featured' || $('filter_from').value == 'archived' || $('filter_from').value == 'child' || $('filter_from').value == 'start' || $('filter_from').value == 'end')
		document.adminForm.submit();
}

function rs_clear_filters() {
	$('rs_clear').value = 1;
	document.adminForm.submit();
}

function rs_remove_filter(key) {
	$('filter_archived').disabled = true;
	$('filter_featured').disabled = true;
	$('filter_child').disabled = true;
	$('filter_start').disabled = true;
	$('filter_end').disabled = true;
	$('rs_remove').value = key;
	document.adminForm.submit();
}

Joomla.submitbutton = function(task) {
	if(task == 'preview') {
		var ids = document.getElementsByName('cid[]');
		var id = '';
		for(i=0;i<ids.length;i++)
			if (ids[i].checked) {
				id = ids[i].value;
				break;
			}
		
		window.open('<?php echo JURI::root(); ?>index.php?option=com_rseventspro&layout=show&id='+id);
		return false;
	} else {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=events'); ?>" name="adminForm" id="adminForm" autocomplete="off">
	<div class="row-fluid">
		<div class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="span10">
			<div id="rs_event_selects">
				<div>
					<div class="rs_select_top" id="rs_select_top1">
						<select id="filter_from" name="filter_from[]" size="1">
							<?php echo JHtml::_('select.options', $this->get('filteroptions')); ?>
						</select>
					</div>
					
					<div class="rs_select_top" id="rs_select_top2">
						<select id="filter_condition" name="filter_condition[]" size="1">
							<?php echo JHtml::_('select.options', $this->get('filterconditions')); ?>
						</select>
					</div>
					
					<div class="rs_select_top" id="rs_select_top5" style="display:none;">
						<select id="filter_archived" name="filter_archived" size="1">
							<option value="1"><?php echo JText::_('JYES'); ?></option>
							<option value="0"><?php echo JText::_('JNO'); ?></option>
							<option value="2"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_ARCHIVED_ONLY'); ?></option>
						</select>
					</div>
					
					<div class="rs_select_top" id="rs_select_top6" style="display: none;">
						<?php echo JHTML::_('rseventspro.calendar', rseventsproHelper::date('now','Y-m-d H:i:s'), 'filter_start', 'filter_start','%Y-%m-%d %H:%M:%S'); ?>
					</div>
					
					<div class="rs_select_top" id="rs_select_top7" style="display: none;">
						<?php echo JHTML::_('rseventspro.calendar', rseventsproHelper::date('now','Y-m-d H:i:s'), 'filter_end', 'filter_end','%Y-%m-%d %H:%M:%S'); ?>
					</div>
					
					<div class="rs_select_top" id="rs_select_top8" style="display: none;">
						<select id="filter_child" name="filter_child" size="1">
							<option value="1"><?php echo JText::_('JYES'); ?></option>
							<option value="0"><?php echo JText::_('JNO'); ?></option>
						</select>
					</div>
					
					<div class="rs_select_top" id="rs_select_top9" style="display: none;">
						<select id="filter_featured" name="filter_featured" size="1">
							<option value="1"><?php echo JText::_('JYES'); ?></option>
							<option value="0"><?php echo JText::_('JNO'); ?></option>
						</select>
					</div>
					
					<input type="text" name="search[]" id="search" onkeyup="rs_search();" onkeydown="rs_stop();" value="" size="35" class="rs_input" />
					<ul class="rs_results" id="rs_results">
						<li></li>
					</ul>
				
					<button type="button" onclick="rs_add_filter();" class="rs_select_button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ADD_FILTER'); ?></button>
					<button type="button" onclick="rs_clear_filters();" class="rs_select_button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></button>
				</div>
				<br />
		
				<div>
					<div class="rs_select_top" id="rs_select_top3">
						<select id="filter_order" name="filter_order" size="1">
							<?php echo JHtml::_('select.options', $this->get('ordering'),'value','text',$this->sortColumn); ?>
						</select>
					</div>
					
					<div class="rs_select_top" id="rs_select_top4">
						<select id="filter_order_Dir" name="filter_order_Dir" size="1">
							<?php echo JHtml::_('select.options', $this->get('order'),'value','text',$this->sortOrder); ?>
						</select>
					</div>
				</div>
				<br /><br />
		
				<div class="rs_filter" style="margin-top: 5px !important;">
					<ul id="rs_filters">
					<?php if (!is_null($archived = $this->other['archived'])) { ?>
						<li>
							<span><?php echo JText::_('COM_RSEVENTSPRO_FILTER_ARCHIVED'); ?></span>
							<strong>
								<?php if ($archived == 0)
										echo JText::_('JNO');
									elseif ($archived == 1)
										echo JText::_('JYES'); 
									elseif ($archived == 2)
										echo JText::_('COM_RSEVENTSPRO_FILTER_ARCHIVED_ONLY');
								?>
							</strong>
							<a class="rsepro_close" href="javascript: void(0);" onclick="rs_remove_filter('archive')"></a>
						</li>
					<?php } ?>
					<?php if (!is_null($featured = $this->other['featured'])) { ?>
						<li>
							<span><?php echo JText::_('COM_RSEVENTSPRO_FILTER_FEATURED'); ?></span>
							<strong>
								<?php if ($featured == 0)
										echo JText::_('JNO');
									elseif ($featured == 1)
										echo JText::_('JYES'); 
								?>
							</strong>
							<a class="rsepro_close" href="javascript: void(0);" onclick="rs_remove_filter('featured')"></a>
						</li>
					<?php } ?>
					<?php if (!is_null($child = $this->other['childs'])) { ?>
						<li>
							<span><?php echo JText::_('COM_RSEVENTSPRO_FILTER_CHILD'); ?></span>
							<strong>
								<?php if ($child == 0)
										echo JText::_('JNO');
									elseif ($child == 1)
										echo JText::_('JYES'); 
								?>
							</strong>
							<a class="rsepro_close" href="javascript: void(0);" onclick="rs_remove_filter('child')"></a>
						</li>
					<?php } ?>
					
					<?php if (!is_null($start = $this->other['start'])) { ?>
						<li>
							<span><?php echo JText::_('COM_RSEVENTSPRO_FILTER_FROM'); ?></span>
							<strong><?php echo $start; ?></strong>
							<a class="rsepro_close" href="javascript: void(0);" onclick="rs_remove_filter('start')"></a>
						</li>
					<?php } ?>
					
					<?php if (!is_null($end = $this->other['end'])) { ?>
						<li>
							<span><?php echo JText::_('COM_RSEVENTSPRO_FILTER_TO'); ?></span>
							<strong><?php echo $end; ?></strong>
							<a class="rsepro_close" href="javascript: void(0);" onclick="rs_remove_filter('end')"></a>
						</li>
					<?php } ?>
					
					<?php if (!empty($this->columns)) { ?>
					<?php for ($i=0; $i<count($this->columns); $i++) { ?>
						<li>
							<span><?php echo rseventsproHelper::translate($this->columns[$i]); ?></span>
							<span><?php echo rseventsproHelper::translate($this->operators[$i]); ?></span>
							<strong><?php echo $this->escape($this->values[$i]); ?></strong>
							<a class="rsepro_close" href="javascript: void(0);" onclick="rs_remove_filter(<?php echo $i; ?>)"></a>
							<input type="hidden" name="filter_from[]" value="<?php echo $this->escape($this->columns[$i]); ?>" />
							<input type="hidden" name="filter_condition[]" value="<?php echo $this->escape($this->operators[$i]); ?>" />
							<input type="hidden" name="search[]" value="<?php echo $this->escape($this->values[$i]); ?>" />
						</li>
					<?php } ?>
					<?php } ?>
					</ul>
				</div>
			</div>
			
			<?php $i = 0; ?>
			<?php $cols = 10; ?>
			<table class="table table-striped adminlist">
				<thead>
					<th width="1%" align="center" class="hidden-phone"><input type="checkbox" name="checkall-toggle" id="rscheckbox" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);"/></th>
					<th width="5%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('JSTATUS'); ?></th>
					<th class="nowrap hidden-phone">&nbsp;</th>
					<th width="40%"><?php echo JText::_('COM_RSEVENTSPRO_TH_EVENT'); ?></th>
					<th width="10%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('COM_RSEVENTSPRO_TH_LOCATION'); ?></th>
					<th width="10%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('COM_RSEVENTSPRO_TH_OWNER'); ?></th>
					<th width="10%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('COM_RSEVENTSPRO_TH_CATEGORIES'); ?></th>
					<th width="10%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('COM_RSEVENTSPRO_TH_TAGS'); ?></th>
					<th width="10%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('COM_RSEVENTSPRO_TH_ENDING'); ?></th>
					<th width="1%" class="nowrap hidden-phone center" align="center"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
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
					<?php $complete = empty($row->completed) ? ' rs_incomplete' : ''; ?>			
					
					<tr class="<?php echo 'row'.$k.$complete; ?>">
						<td align="center" class="center hidden-phone" style="vertical-align:middle;"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
						<td align="center" class="center hidden-phone" style="vertical-align:middle;">
							<div class="btn-group">
								<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
								<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
							</div>
						</td>
						<td class="hidden-phone">
							<div class="rs_event_img">
								<?php $image = !empty($row->icon) ? 'events/thumbs/s_'.$row->icon : 'blank.png'; ?>
								<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/<?php echo $image; ?>" alt="" width="70" />
							</div>
						</td>
						<td class="has-context">
							<?php if ($stars) { ?>
							<div class="rs_stars">
								<ul class="rsepro_star_rating">
									<li id="rsepro_current_rating" class="rsepro_feedback_selected_<?php echo $stars; ?>">&nbsp;</li>
								</ul>
							</div>
							<?php } ?>
							<div class="rs_event_details">
								<p>
									<b><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$row->id); ?>"><?php echo $row->name; ?></a></b>
									<?php if (empty($row->completed)) echo '<b>'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</b>'; ?>
									<?php echo rseventsproHelper::report($row->id); ?>
								</p>
								<p><?php echo $row->allday ? rseventsproHelper::date($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::date($row->start,null,true); ?></p>
								<?php if ($availabletickets = $this->getTickets($row->id)) { ?>
								<p><?php echo $availabletickets; ?></p>
								<?php } ?>
								<?php if ($subscriptions = $this->getSubscribers($row->id)) { ?>
								<p><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a></p>
								<?php } ?>
							</div>
							<?php if ($row->parent) { ?>
							<div class="rs_child">
								<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/baloon.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO'); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO'); ?>" />
							</div>
							<?php } ?>
						</td>
						<td align="center" class="center hidden-phone"><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=location.edit&id='.$row->lid); ?>"><?php echo $row->lname; ?></a></td>
						<td align="center" class="center hidden-phone"><?php echo empty($row->owner) ? JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST') : $row->uname; ?></td>
						<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::categories($row->id, true); ?></td>
						<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::tags($row->id,true); ?></td>
						<td align="center" class="center hidden-phone"><?php echo $row->allday ? '' : rseventsproHelper::date($row->end,null,true); ?></td>
						<td class="center hidden-phone"><?php echo $id; ?></td>
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
					<?php $complete = empty($row->completed) ? ' rs_incomplete' : ''; ?>			
					
					<tr class="<?php echo 'row'.$k.$complete; ?>">
						<td align="center" class="center hidden-phone" style="vertical-align:middle;"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
						<td align="center" class="center hidden-phone" style="vertical-align:middle;">
							<div class="btn-group">
								<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
								<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
							</div>
						</td>
						<td class="hidden-phone">
							<div class="rs_event_img">
								<?php $image = !empty($row->icon) ? 'events/thumbs/s_'.$row->icon : 'blank.png'; ?>
								<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/<?php echo $image; ?>" alt="" width="70" />
							</div>
						</td>
						<td class="nowrap has-context">
							<?php if ($stars) { ?>
							<div class="rs_stars">
								<ul class="rsepro_star_rating">
									<li id="rsepro_current_rating" class="rsepro_feedback_selected_<?php echo $stars; ?>">&nbsp;</li>
								</ul>
							</div>
							<?php } ?>
							<div class="rs_event_details">
								<p>
									<b><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$row->id); ?>"><?php echo $row->name; ?></a></b>
									<?php if (empty($row->completed)) echo '<b>'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</b>'; ?>
									<?php echo rseventsproHelper::report($row->id); ?>
								</p>
								<p><?php echo $row->allday ? rseventsproHelper::date($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::date($row->start,null,true); ?></p>
								<?php if ($availabletickets = $this->getTickets($row->id)) { ?>
								<p><?php echo $availabletickets; ?></p>
								<?php } ?>
								<?php if ($subscriptions = $this->getSubscribers($row->id)) { ?>
								<p><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a></p>
								<?php } ?>
							</div>
							<?php if ($row->parent) { ?>
							<div class="rs_child">
								<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/baloon.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO'); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO'); ?>" />
							</div>
							<?php } ?>
						</td>
						<td align="center" class="center hidden-phone"><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=location.edit&id='.$row->lid); ?>"><?php echo $row->lname; ?></a></td>
						<td align="center" class="center hidden-phone"><?php echo empty($row->owner) ? JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST') : $row->uname; ?></td>
						<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::categories($row->id, true); ?></td>
						<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::tags($row->id,true); ?></td>
						<td align="center" class="center hidden-phone"><?php echo $row->allday ? '' : rseventsproHelper::date($row->end,null,true); ?></td>
						<td class="center hidden-phone"><?php echo $id; ?></td>
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
					<?php $complete = empty($row->completed) ? ' rs_incomplete' : ''; ?>			
					
					<tr class="<?php echo 'row'.$k.$complete; ?>">
						<td align="center" class="center hidden-phone" style="vertical-align:middle;"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
						<td align="center" class="center hidden-phone" style="vertical-align:middle;">
							<div class="btn-group">
								<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
								<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
							</div>
						</td>
						<td class="hidden-phone">
							<div class="rs_event_img">
								<?php $image = !empty($row->icon) ? 'events/thumbs/s_'.$row->icon : 'blank.png'; ?>
								<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/<?php echo $image; ?>" alt="" width="70" />
							</div>
						</td>
						<td class="nowrap has-context">
							<?php if ($stars) { ?>
							<div class="rs_stars">
								<ul class="rsepro_star_rating">
									<li id="rsepro_current_rating" class="rsepro_feedback_selected_<?php echo $stars; ?>">&nbsp;</li>
								</ul>
							</div>
							<?php } ?>
							<div class="rs_event_details">
								<p>
									<b><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$row->id); ?>"><?php echo $row->name; ?></a></b>
									<?php if (empty($row->completed)) echo '<b>'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</b>'; ?>
									<?php echo rseventsproHelper::report($row->id); ?>
								</p>
								<p><?php echo $row->allday ? rseventsproHelper::date($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::date($row->start,null,true); ?></p>
								<?php if ($availabletickets = $this->getTickets($row->id)) { ?>
								<p><?php echo $availabletickets; ?></p>
								<?php } ?>
								<?php if ($subscriptions = $this->getSubscribers($row->id)) { ?>
								<p><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a></p>
								<?php } ?>
							</div>
							<?php if ($row->parent) { ?>
							<div class="rs_child">
								<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/baloon.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO'); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO'); ?>" />
							</div>
							<?php } ?>
						</td>
						<td align="center" class="center hidden-phone"><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=location.edit&id='.$row->lid); ?>"><?php echo $row->lname; ?></a></td>
						<td align="center" class="center hidden-phone"><?php echo empty($row->owner) ? JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST') : $row->uname; ?></td>
						<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::categories($row->id, true); ?></td>
						<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::tags($row->id,true); ?></td>
						<td align="center" class="center hidden-phone"><?php echo $row->allday ? '' : rseventsproHelper::date($row->end,null,true); ?></td>
						<td class="center hidden-phone"><?php echo $id; ?></td>
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
					<?php $complete = empty($row->completed) ? ' rs_incomplete' : ''; ?>			
					
					<tr class="<?php echo 'row'.$k.$complete; ?>">
						<td align="center" class="center hidden-phone" style="vertical-align:middle;"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
						<td align="center" class="center hidden-phone" style="vertical-align:middle;">
							<div class="btn-group">
								<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
								<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
							</div>
						</td>
						<td class="hidden-phone">
							<div class="rs_event_img">
								<?php $image = !empty($row->icon) ? 'events/thumbs/s_'.$row->icon : 'blank.png'; ?>
								<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/<?php echo $image; ?>" alt="" width="70" />
							</div>
						</td>
						<td class="nowrap has-context">
							<?php if ($stars) { ?>
							<div class="rs_stars">
								<ul class="rsepro_star_rating">
									<li id="rsepro_current_rating" class="rsepro_feedback_selected_<?php echo $stars; ?>">&nbsp;</li>
								</ul>
							</div>
							<?php } ?>
							<div class="rs_event_details">
								<p>
									<b><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$row->id); ?>"><?php echo $row->name; ?></a></b>
									<?php if (empty($row->completed)) echo '<b>'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</b>'; ?>
									<?php echo rseventsproHelper::report($row->id); ?>
								</p>
								<p><?php echo $row->allday ? rseventsproHelper::date($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::date($row->start,null,true); ?></p>
								<?php if ($availabletickets = $this->getTickets($row->id)) { ?>
								<p><?php echo $availabletickets; ?></p>
								<?php } ?>
								<?php if ($subscriptions = $this->getSubscribers($row->id)) { ?>
								<p><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a></p>
								<?php } ?>
							</div>
							<?php if ($row->parent) { ?>
							<div class="rs_child">
								<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/baloon.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO'); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO'); ?>" />
							</div>
							<?php } ?>
						</td>
						<td align="center" class="center hidden-phone"><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=location.edit&id='.$row->lid); ?>"><?php echo $row->lname; ?></a></td>
						<td align="center" class="center hidden-phone"><?php echo empty($row->owner) ? JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST') : $row->uname; ?></td>
						<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::categories($row->id, true); ?></td>
						<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::tags($row->id,true); ?></td>
						<td align="center" class="center hidden-phone"><?php echo $row->allday ? '' : rseventsproHelper::date($row->end,null,true); ?></td>
						<td class="center hidden-phone"><?php echo $id; ?></td>
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
					<?php $complete = empty($row->completed) ? ' rs_incomplete' : ''; ?>			
					
					<tr class="<?php echo 'row'.$k.$complete; ?>">
						<td align="center" class="center hidden-phone" style="vertical-align:middle;"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
						<td align="center" class="center hidden-phone" style="vertical-align:middle;">
							<div class="btn-group">
								<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
								<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
							</div>
						</td>
						<td class="hidden-phone">
							<div class="rs_event_img">
								<?php $image = !empty($row->icon) ? 'events/thumbs/s_'.$row->icon : 'blank.png'; ?>
								<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/<?php echo $image; ?>" alt="" width="70" />
							</div>
						</td>
						<td class="nowrap has-context">
							<?php if ($stars) { ?>
							<div class="rs_stars">
								<ul class="rsepro_star_rating">
									<li id="rsepro_current_rating" class="rsepro_feedback_selected_<?php echo $stars; ?>">&nbsp;</li>
								</ul>
							</div>
							<?php } ?>
							<div class="rs_event_details">
								<p>
									<b><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$row->id); ?>"><?php echo $row->name; ?></a></b>
									<?php if (empty($row->completed)) echo '<b>'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</b>'; ?>
									<?php echo rseventsproHelper::report($row->id); ?>
								</p>
								<p><?php echo $row->allday ? rseventsproHelper::date($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::date($row->start,null,true); ?></p>
								<?php if ($availabletickets = $this->getTickets($row->id)) { ?>
								<p><?php echo $availabletickets; ?></p>
								<?php } ?>
								<?php if ($subscriptions = $this->getSubscribers($row->id)) { ?>
								<p><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a></p>
								<?php } ?>
							</div>
							<?php if ($row->parent) { ?>
							<div class="rs_child">
								<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/baloon.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO'); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO'); ?>" />
							</div>
							<?php } ?>
						</td>
						<td align="center" class="center hidden-phone"><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=location.edit&id='.$row->lid); ?>"><?php echo $row->lname; ?></a></td>
						<td align="center" class="center hidden-phone"><?php echo empty($row->owner) ? JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST') : $row->uname; ?></td>
						<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::categories($row->id, true); ?></td>
						<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::tags($row->id,true); ?></td>
						<td align="center" class="center hidden-phone"><?php echo $row->allday ? '' : rseventsproHelper::date($row->end,null,true); ?></td>
						<td class="center hidden-phone"><?php echo $id; ?></td>
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
					<?php $complete = empty($row->completed) ? ' rs_incomplete' : ''; ?>			
					
					<tr class="<?php echo 'row'.$k.$complete; ?>">
						<td align="center" class="center hidden-phone" style="vertical-align:middle;"><?php echo JHTML::_('grid.id',$i,$row->id); ?></td>
						<td align="center" class="center hidden-phone" style="vertical-align:middle;">
							<div class="btn-group">
								<?php echo JHTML::_('jgrid.published', $row->published, $i, 'events.'); ?>
								<?php echo JHtml::_('rseventspro.featured', $row->featured, $i); ?>
							</div>
						</td>
						<td class="hidden-phone">
							<div class="rs_event_img">
								<?php $image = !empty($row->icon) ? 'events/thumbs/s_'.$row->icon : 'blank.png'; ?>
								<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/<?php echo $image; ?>" alt="" width="70" />
							</div>
						</td>
						<td class="nowrap has-context">
							<?php if ($stars) { ?>
							<div class="rs_stars">
								<ul class="rsepro_star_rating">
									<li id="rsepro_current_rating" class="rsepro_feedback_selected_<?php echo $stars; ?>">&nbsp;</li>
								</ul>
							</div>
							<?php } ?>
							<div class="rs_event_details">
								<p>
									<b><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$row->id); ?>"><?php echo $row->name; ?></a></b>
									<?php if (empty($row->completed)) echo '<b>'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</b>'; ?>
									<?php echo rseventsproHelper::report($row->id); ?>
								</p>
								<p><?php echo $row->allday ? rseventsproHelper::date($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::date($row->start,null,true); ?></p>
								<?php if ($availabletickets = $this->getTickets($row->id)) { ?>
								<p><?php echo $availabletickets; ?></p>
								<?php } ?>
								<?php if ($subscriptions = $this->getSubscribers($row->id)) { ?>
								<p><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id); ?>"><?php echo JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions); ?></a></p>
								<?php } ?>
							</div>
							<?php if ($row->parent) { ?>
							<div class="rs_child">
								<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/baloon.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO'); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO'); ?>" />
							</div>
							<?php } ?>
						</td>
						<td align="center" class="center hidden-phone"><a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=location.edit&id='.$row->lid); ?>"><?php echo $row->lname; ?></a></td>
						<td align="center" class="center hidden-phone"><?php echo empty($row->owner) ? JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST') : $row->uname; ?></td>
						<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::categories($row->id, true); ?></td>
						<td align="center" class="center hidden-phone"><?php echo rseventsproHelper::tags($row->id,true); ?></td>
						<td align="center" class="center hidden-phone"><?php echo $row->allday ? '' : rseventsproHelper::date($row->end,null,true); ?></td>
						<td class="center hidden-phone"><?php echo $id; ?></td>
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
		</div>
	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="total_past" id="total_past" value="<?php echo $this->total_past; ?>" />
	<input type="hidden" name="total_ongoing" id="total_ongoing" value="<?php echo $this->total_ongoing; ?>" />
	<input type="hidden" name="total_thisweek" id="total_thisweek" value="<?php echo $this->total_thisweek; ?>" />
	<input type="hidden" name="total_thismonth" id="total_thismonth" value="<?php echo $this->total_thismonth; ?>" />
	<input type="hidden" name="total_nextmonth" id="total_nextmonth" value="<?php echo $this->total_nextmonth; ?>" />
	<input type="hidden" name="total_upcoming" id="total_upcoming" value="<?php echo $this->total_upcoming; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="rs_clear" id="rs_clear" value="0" />
	<input type="hidden" name="rs_remove" id="rs_remove" value="" />
</form>