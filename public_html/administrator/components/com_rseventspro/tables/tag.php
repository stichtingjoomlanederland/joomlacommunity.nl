<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproTableTag extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	public function __construct($db) {
		parent::__construct('#__rseventspro_tags', 'id', $db);
	}
	
	/**
	 * Overrides JTable::store to set modified data and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function store($updateNulls = false) {
		// Verify that the alias is unique
		$table = JTable::getInstance('Tag', 'rseventsproTable');
		if ($table->load(array('name' => $this->name)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(JText::_('COM_RSEVENTSPRO_DUPLICATE_TAG_NAME'));
			return false;
		}
		return parent::store($updateNulls);
	}
}