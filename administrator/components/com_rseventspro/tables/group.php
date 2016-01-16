<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproTableGroup extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	public function __construct($db) {
		parent::__construct('#__rseventspro_groups', 'id', $db);
	}
	
	/**
	 * Overloaded check function
	 *
	 * @return	boolean
	 * @see		JTable::check
	 * @since	1.5
	 */
	public function check() {
		if (isset($this->jgroups) && is_array($this->jgroups)) {
			$registry = new JRegistry;
			$registry->loadArray($this->jgroups);
			$this->jgroups = (string) $registry;
		}
		
		if (isset($this->jusers) && is_array($this->jusers)) {
			$registry = new JRegistry;
			$registry->loadArray($this->jusers);
			$this->jusers = (string) $registry;
		} else $this->jusers = '';
		
		if (isset($this->event) && is_array($this->event)) {
			$registry = new JRegistry;
			$registry->loadArray($this->event);
			$this->event = (string) $registry;
		} else $this->event = '';
		
		// Check for required data
		if (empty($this->jgroups) && empty($this->jusers)) {
			$this->setError(JText::_('COM_RSEVENTSPRO_GROUPS_ERROR'));
			return false;
		}
		
		return true;
	}
}