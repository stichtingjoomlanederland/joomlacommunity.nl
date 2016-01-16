<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

echo 'RS_DELIMITER0';
if (!empty($this->items)) {
	foreach ($this->items as $i => $item) {
		$offset = $this->pagination->getRowOffset($i) - 1;
		$payment = rseventsproHelper::getPayment($item->gateway);
	
		echo '<tr class="row'.($offset % 2).'">';
		echo '<td class="center">'.JHtml::_('grid.id', $offset, $item->id).'</td>';
		echo '<td class="nowrap has-context">';
		echo '<a href="'.JRoute::_('index.php?option=com_rseventspro&task=subscription.edit&id='.$item->id).'">'.$item->name.'</a> <br />';
		echo rseventsproHelper::showdate($item->date,null,true).' <br />';
		echo '<a href="'.JRoute::_('index.php?option=com_rseventspro&task=subscription.edit&id='.$item->id).'">'.$item->email.'</a> - '.$this->getUser($item->idu).' - '.$item->ip.'</td>';
		echo '<td class="center nowrap has-context">';
		echo '<a href="'.JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$item->ide).'">'.$item->event.'</a> <br />';
		
		if ($item->allday)
			echo rseventsproHelper::showdate($item->start, rseventsproHelper::getConfig('global_date')).'</td>';
		else
			echo '('.rseventsproHelper::showdate($item->start).' - '.rseventsproHelper::showdate($item->end).')</td>';
		
		echo '<td class="center hidden-phone">'.rseventsproHelper::getUserTickets($item->id, true).'</td>';
		echo '<td class="center hidden-phone">'.rseventsproHelper::currency(rseventsproHelper::total($item->id)).'</td>';
		echo '<td class="center hidden-phone">'.($payment ? $payment : '-').'</td>';
		echo '<td class="center hidden-phone">'.$this->getStatus($item->state).'</td>';
		echo '<td class="center hidden-phone">'.($item->confirmed ? JText::_('JYES') : JText::_('JNO')).'</td>';
		echo '<td class="center hidden-phone">'.$item->id.'</td>';
		echo '</tr>';
	}
}
echo 'RS_DELIMITER1';
JFactory::getApplication()->close();