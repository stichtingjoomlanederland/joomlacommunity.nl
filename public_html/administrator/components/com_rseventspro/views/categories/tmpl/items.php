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
		$offset		= $this->pagination->getRowOffset($i) - 1;
		$orderkey   = array_search($item->id, $this->ordering[$item->parent_id]);
		
		// Get the parents of item for sorting
		if ($item->level > 1) {
			$parentsStr = '';
			$_currentParentId = $item->parent_id;
			$parentsStr = ' ' . $_currentParentId;
			for ($i2 = 0; $i2 < $item->level; $i2++) {
				foreach ($this->ordering as $k => $v) {
					$v = implode('-', $v);
					$v = '-' . $v . '-';
					if (strpos($v, '-' . $_currentParentId . '-') !== false) {
						$parentsStr .= ' ' . $k;
						$_currentParentId = $k;
						break;
					}
				}
			}
		} else {
			$parentsStr = "";
		}
		
		echo '<tr class="row'.($offset % 2).'" sortable-group-id="'.$item->parent_id.'" item-id="'.$item->id.'" parents="'.$parentsStr.'" level="'.$item->level.'">';
		echo $this->filterbar->orderingBody($orderkey + 1, 'a.lft', $this->pagination, $offset, $n, 'categories');
		echo '<td class="center">'.JHtml::_('grid.id', $offset, $item->id).'</td>';
		echo '<td class="center">'.JHtml::_('jgrid.published', $item->published, $offset, 'categories.').'</td>';
		echo '<td class="nowrap has-context">';
		echo str_repeat('<span class="gi">&mdash;</span>', $item->level - 1);
		echo ' <a href="'.JRoute::_('index.php?option=com_rseventspro&task=category.edit&id='.$item->id).'">'.$this->escape($item->title).'</a>';
		echo ' <span class="small" title="'.$this->escape($item->path).'">';
		echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));
		echo '</span>';
		echo '</td>';
		echo '<td class="small center hidden-phone" align="center">'.$this->escape($item->access_level).'</td>';
		echo '<td class="small nowrap hidden-phone center" align="center">';
		
		if ($item->language == '*') {
			echo JText::alt('JALL', 'language');
		} else {
			echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED');
		}
		
		echo '</td>';
		echo '<td class="center hidden-phone center" align="center">';
		echo '<span title="'.sprintf('%d-%d', $item->lft, $item->rgt).'">';
		echo (int) $item->id;
		echo '</span>';
		echo '</td>';
		echo '</tr>';
	}
}
echo 'RS_DELIMITER1';
JFactory::getApplication()->close();