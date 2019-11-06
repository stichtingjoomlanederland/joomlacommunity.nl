<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_rseventspro
 * @since       1.6
 */
class JFormFieldGcalendars extends JFormFieldList
{
	/**
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'Gcalendars';
	
	protected function getInput() {
		if (!is_array($this->value)) {		
			if (strpos($this->value, ',') !== false) {
				$this->setValue(explode(',',$this->value));
			}
		}
		
		return parent::getInput();
	}
	
	protected function getOptions() {
		$options = array();
		
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/google.php';
		
		try {
			$google	= new RSEPROGoogle();
			if ($calendars = $google->getCalendars()) {
				foreach ($calendars as $id => $name) {
					$options[] = JHtml::_('select.option', $id, $name);
				}
			}
		} catch(Exception $e) {
			JFactory::getApplication()->enqueueMessage('[Google Calendar] '.$e->getMessage(), 'error');
		}
			
		return $options;
	}
}