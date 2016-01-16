<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

echo 'RS_DELIMITER0';
$n = count($this->items);
if (!empty($this->items)) {
	foreach ($this->items as $i => $item) {
		$offset = $this->pagination->getRowOffset($i) - 1;
		
		echo '<tr class="row'.($offset % 2).'" sortable-group-id="1">';
		echo $this->filterbar->orderingBody($item->ordering, 'ordering', $this->pagination, $offset, $n, 'locations');
		echo '<td class="center" align="center">'.JHtml::_('grid.id', $offset, $item->id).'</td>';
		echo '<td class="nowrap has-context">';
		echo '<a href="'.JRoute::_('index.php?option=com_rseventspro&task=location.edit&id='.$item->id).'">'.$item->name.'</a>';
		
		if (!empty($item->address)) {
			echo '<br />'; 
			echo '<small>'.$item->address.'</small>';
		}
		echo '</td>';
		echo '<td class="center" align="center">'.JHtml::_('jgrid.published', $item->published, $offset, 'locations.').'</td>';
		echo '<td class="center hidden-phone" align="center">'.$item->id.'</td>';
		echo '</tr>';
	}
}
echo 'RS_DELIMITER1';
JFactory::getApplication()->close();