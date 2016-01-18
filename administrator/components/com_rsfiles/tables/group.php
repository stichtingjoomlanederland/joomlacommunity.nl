<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesTableGroup extends JTable
{	
	/**
	 * @param	JDatabase	A database connector object
	 */
	public function __construct($db) {
		parent::__construct('#__rsfiles_groups', 'IdGroup', $db);
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
		} else $this->jgroups = '';
		
		if (isset($this->jusers) && is_array($this->jusers)) {
			$registry = new JRegistry;
			$registry->loadArray($this->jusers);
			$this->jusers = (string) $registry;
		} else $this->jusers = '';
		
		// Check for required data
		if (empty($this->jgroups) && empty($this->jusers)) {
			$this->setError(JText::_('COM_RSFILES_GROUPS_ERROR'));
			return false;
		}
		
		return true;
	}
}