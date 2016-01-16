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
		echo '<td align="center" class="center" style="vertical-align: middle;">'.JHTML::_('grid.id',$i,$row->id).'</td>';
		echo '<td align="center" class="center hidden-phone" style="vertical-align: middle;"><div class="btn-group">'.JHTML::_('jgrid.published', $row->published, $i, 'events.').JHtml::_('rseventspro.featured', $row->featured, $i).'</div></td>';
		echo '<td class="hidden-phone">';
		echo '<div class="rs_event_img">';
		echo '<img src="'.JURI::root().'index.php?option=com_rseventspro&task=image&id='.$row->id.'&width=70&nocache='.uniqid('').'" alt="" width="70" />';
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
		
		if (empty($row->completed)) 
			echo '<b>'.JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT').'</b>';
		
		echo rseventsproHelper::report($row->id);
		echo '</p>';
		
		if ($row->allday)
			echo '<p>'.rseventsproHelper::showdate($row->start,rseventsproHelper::getConfig('global_date'),true).'</p>';
		else
			echo '<p>'.rseventsproHelper::showdate($row->start,null,true).'</p>';
		
		
		if ($availabletickets = $this->getTickets($row->id)) {
			echo '<p>'.$availabletickets.'</p>';
		}
		
		if ($subscriptions = $this->getSubscribers($row->id)) {
			echo '<p><a href="'.JRoute::_('index.php?option=com_rseventspro&view=subscriptions&filter_event='.$row->id).'">'.JText::plural('COM_RSEVENTSPRO_SUBSCRIBERS_NO',$subscriptions).'</a></p>';
		}
		
		echo '</div>';
		
		echo '</td>';
		echo '<td align="center" class="center hidden-phone"><a href="'.JRoute::_('index.php?option=com_rseventspro&task=location.edit&id='.$row->lid).'">'.$row->lname.'</a></td>';
		echo '<td align="center" class="center hidden-phone">'.(empty($row->owner) ? JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST') : $row->uname).'</td>';
		echo '<td align="center" class="center hidden-phone">'.rseventsproHelper::categories($row->id, true).'</td>';
		echo '<td align="center" class="center hidden-phone">'.rseventsproHelper::tags($row->id,true).'</td>';
		
		if ($row->allday)
			echo '<td align="center" class="center hidden-phone"></td>';
		else
			echo '<td align="center" class="center hidden-phone">'.rseventsproHelper::showdate($row->end,null,true).'</td>';
		
		echo '<td align="center" class="center hidden-phone">'.$row->hits.'</td>';
		echo '<td class="center hidden-phone">'.$id.'</td>';
		echo '</tr>';
		
		$i++;
		$k = 1-$k;
	}
}
echo 'RS_DELIMITER1';
JFactory::getApplication()->close();