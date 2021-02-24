<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

class JFormFieldRSWaitinglisttime extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSWaitinglisttime';

	protected function getInput() {
		$html	= array();
		$class	= !rseventsproHelper::isJ4() && JFactory::getApplication()->isClient('site') ? ' input-small' : '';
		
		$html[] = '<div class="'.RSEventsproAdapterGrid::row().'">';
		$html[] = '<div class="'.RSEventsproAdapterGrid::column(4).'">'.RSEventsproAdapterGrid::inputGroup('<input type="text" class="form-control'.$class.'" name="'.$this->name.'[]" value="'.$this->value[0].'" />',null, JText::_('COM_RSEVENTSPRO_DAYS')).'</div>';
		$html[] = '<div class="'.RSEventsproAdapterGrid::column(4).'">'.RSEventsproAdapterGrid::inputGroup('<input type="text" class="form-control'.$class.'" name="'.$this->name.'[]" value="'.$this->value[1].'" />',null, JText::_('COM_RSEVENTSPRO_HOURS')).'</div>';
		$html[] = '<div class="'.RSEventsproAdapterGrid::column(4).'">'.RSEventsproAdapterGrid::inputGroup('<input type="text" class="form-control'.$class.'" name="'.$this->name.'[]" value="'.$this->value[2].'" />',null, JText::_('COM_RSEVENTSPRO_MINUTES')).'</div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
}