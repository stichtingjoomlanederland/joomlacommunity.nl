<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');
class JFormFieldEvents extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Events';
	
	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getOptions() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$active	= rseventsproHelper::getConfig('active_events');
		$tz		= JFactory::getConfig()->get('offset');
		$return = array();
		
		$today = JFactory::getDate();
		$today->setTime(0,0,0);
		$today = $today->toSql();
		
		$today = JFactory::getDate($today, $tz);
		$today->setTimezone(new DateTimezone('UTC'));
		$today = $today->toSql();
		
		$query->select($db->qn('id'))
			->select($db->qn('name'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('registration').' = '.$db->q(1))
			->where($db->qn('published').' = '.$db->q(1))
			->where($db->qn('completed').' = '.$db->q(1))
			->order($db->qn('start').' ASC');
		
		if ($active) {
			$query->where('(('.$db->qn('end').' >= '.$db->q(JFactory::getDate()->toSql()).' AND '.$db->qn('end').' != '.$db->q($db->getNullDate()).') OR ('.$db->qn('end').' = '.$db->q($db->getNullDate()).' AND '.$db->qn('start').' >= '.$db->q($today).'))');
		}
		
		$db->setQuery($query);
		if ($events = $db->loadObjectList()) {
			foreach ($events as $event) {
				$event->name .= ' ('.rseventsproHelper::showdate($event->start).')';
				$return[] = JHtml::_('select.option', $event->id, $event->name);
			}
		}
		
		return $return;
	}
}