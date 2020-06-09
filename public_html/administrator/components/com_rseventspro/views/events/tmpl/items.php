<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

echo 'RS_DELIMITER0';
$k = 0;
$i = $this->total;
$n = count($this->data);
if (!empty($this->data))
{
	foreach ($this->data as $id) {
		$row = $this->getDetails($id);
		$stars = rseventsproHelper::stars($row->id);
		$remaining = 5 - (int) $stars;
		$complete = empty($row->completed) ? ' rs_incomplete' : '';
		
		echo '<tr class="row'.$k.$complete.'">';
		echo '<td align="center" class="center">'.JHTML::_('grid.id',$i,$row->id).'</td>';
		echo '<td align="center" class="center hidden-phone"><div class="btn-group">';
		echo JHTML::_('jgrid.published', $row->published, $i, 'events.').JHtml::_('rseventspro.featured', $row->featured, $i);
		echo '<a class="btn btn-micro hasTooltip" title="'.JText::_('COM_RSEVENTSPRO_PREVIEW_EVENT').'" target="_blank" href="'.JUri::root().'index.php?option=com_rseventspro&layout=show&id='.$row->id.'"><span class="icon-zoom-in"></span></a>';
		echo '</div></td>';
		echo '<td class="hidden-phone center">';
		echo '<div class="rs_event_img">';
		echo '<img src="'.rseventsproHelper::thumb($row->id, 70).'?nocache='.uniqid('').'" alt="" width="70" />';
		echo '</div>';
		echo '</td>';
		echo '<td class="has-context">';
		
		if ($stars) {
			echo '<div class="rs_stars">';
			for ($i=0;$i<$stars;$i++) {
				echo '<i class="fa fa-star" style="color: #e3cf7a;"></i>';
			}
			for ($i=0;$i<$remaining;$i++) {
				echo '<i class="fa fa-star-o"></i>';
			}
			echo '</div>';
		}
		
		echo '<div class="rs_event_details">';
		echo '<p>';
		
		if ($row->parent) {
			echo '<i class="fa fa-child" title="'.ucfirst(JText::_('COM_RSEVENTSPRO_CHILD_EVENT_INFO')).'"></i> ';
		}
						
		echo '<b><a href="'.JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$row->id).'">'.$row->name.'</a></b>';
		
		if (empty($row->completed)) {
			echo ' <small class="muted">'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</small>';
		}
		
		if ($row->published == 3) {
			echo ' <small class="muted text-error">'.JText::_('COM_RSEVENTSPRO_GLOBAL_CANCELED_EVENT').'</small>';
		}
		
		echo ' '.rseventsproHelper::report($row->id);
		echo '</p>';
					
		echo '<p>';
		echo '<small class="muted">';
		echo $row->allday ? rseventsproHelper::showdate($row->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::showdate($row->start,null,true).' - '.rseventsproHelper::showdate($row->end,null,true);
		echo '</small>';
		echo '</p>';
					
		$availabletickets = $row->registration ? $this->getTickets($row->id) : false;
		$subscriptions = $row->registration ? $this->getSubscribers($row->id) : false;
		$waitinglist = $row->registration ? $this->getWaitingList($row->id) : false;
		$unsubscribers = $row->registration ? $this->getUnsubscribers($row->id) : false;
					
		if ($availabletickets) { 
			echo '<p><small class="muted">'.$availabletickets.'</small></p>';
		}
					
		echo '<p>';
		if ($subscriptions) {
			echo '<a class="btn btn-small" href="'.JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id).'">'.JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions).'</a> ';
		}
		
		if ($waitinglist) {
			echo '<a class="btn btn-small" href="'.JRoute::_('index.php?option=com_rseventspro&view=waitinglist&id='.$row->id).'">'.JText::_('COM_RSEVENTSPRO_WAITINGLIST').'</a> ';
		}
		
		if ($row->rsvp) {
			echo '<a class="btn btn-small" href="'.JRoute::_('index.php?option=com_rseventspro&view=rsvp&id='.$row->id).'">'.JText::_('COM_RSEVENTSPRO_RSVP_GUESTS').'</a> ';
		}
		
		if ($unsubscribers) {
			echo '<a class="btn btn-small" href="'.JRoute::_('index.php?option=com_rseventspro&view=unsubscribers&id='.$row->id).'">'.JText::_('COM_RSEVENTSPRO_UNSUBSCRIBERS').'</a> ';
		}
		
		echo '</p>';
		echo '</div>';
		echo '</td>';
		
		echo '<td class="hidden-phone">';
		$categories = rseventsproHelper::categories($row->id, true, ', ');
		$tags = rseventsproHelper::tags($row->id, true);
		
		echo '<p><i class="fa fa-user fa-fw"></i> '.(empty($row->owner) ? JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST') : $row->uname).'</p>';
		
		if ($row->lid) {
			echo '<p><i class="fa fa-map-marker fa-fw"></i> <a href="'.JRoute::_('index.php?option=com_rseventspro&task=location.edit&id='.$row->lid).'">'.$row->lname.'</a></p>';
		}
		
		if ($categories) { 
			echo '<p><i class="fa fa-book fa-fw"></i> '.$categories.'</p>';
		}
		
		if ($tags) { 
			echo '<p><i class="fa fa-tags fa-fw"></i> '.$tags.'</p>';
		}
		
		echo '</td>';
		echo '<td align="center" class="center hidden-phone">'.$row->hits.'</td>';
		echo '<td class="center hidden-phone">'.$id.'</td>';
		echo '</tr>';
		
		$i++;
		$k = 1-$k;
	}
}
echo 'RS_DELIMITER1';
JFactory::getApplication()->close();