<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproTableLocation extends JTable
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
		// Let's check the coordinates 		
		try {
			$this->coordinates = rseventsproHelper::checkCoordinates($this->coordinates);
		} catch(Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
		
		// Set ordering
		if (empty($this->id)) {
			$this->ordering = self::getNextOrder();
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