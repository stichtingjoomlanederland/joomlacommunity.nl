<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproTableLocation extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	public function __construct($db) {
		parent::__construct('#__rseventspro_locations', 'id', $db);
	}
	
	/**
	 * Overloaded check function
	 *
	 * @return	boolean
	 * @see		JTable::check
	 * @since	1.5
	 */
	public function check() {
		// Set ordering
		if (empty($this->id)) {
			$this->ordering = self::getNextOrder();
		}

		$jinput = JFactory::getApplication()->input->get('jform',array(),'array');
		if (isset($jinput['config']) && !empty($jinput['config']['enable_google_maps'])) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->clear();
			$query->update($db->qn('#__rseventspro_config'))
				->set($db->qn('value').' = '.$db->q($jinput['config']['enable_google_maps']))
				->where($db->qn('name').' = '.$db->q('enable_google_maps'));
			
			$db->setQuery($query);
			$db->execute();
		}
		
		if (isset($this->gallery_tags) && is_array($this->gallery_tags)) {
			$registry = new JRegistry;
			$registry->loadArray($this->gallery_tags);
			$this->gallery_tags = (string) $registry;
		} else {
			$this->gallery_tags = '';
		}
		
		return true;
	}
}