<?php
/**
 * @version		$Id: featured.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	SPUpgrade
 */
class SPUpgradeTableTables extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__spupgrade_tables', 'id', $db);
	}
    
    /**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 * set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found or on error (internal error state set in that case).
	 *
	 * @link    http://docs.joomla.org/JTable/load
	 * @since   11.1
	 */
	public function load($keys = null, $reset = true)
	{        
		if (empty($keys))
		{
			// If empty, use the value of the current key
			$keyName = $this->_tbl_key;
			$keyValue = $this->keyName;

			// If empty primary key there's is no need to load anything
			if (empty($keyValue))
			{
				return true;
			}

			$keys = array($keyName => $keyValue);
		}
		elseif (!is_array($keys))
		{
			// Load by primary key.
			$keys = array($this->_tbl_key => $keys);
		}

		if ($reset)
		{
			$this->reset();
		}

		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from($this->_tbl);
		$fields = array_keys($this->getProperties());

		foreach ($keys as $field => $value)
		{
			// Check that $field is in the table.
			if (!in_array($field, $fields))
			{
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_CLASS_IS_MISSING_FIELD', get_class($this), $field));
				$this->setError($e);
				return false;
			}
			// Add the search tuple to the query.
			$query->where($this->_db->quoteName($field) . ' = ' . $this->_db->quote($value));
		}

		$this->_db->setQuery($query);

		try
		{
			$row = $this->_db->loadAssoc();
		}
		catch (RuntimeException $e)
		{
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}

		// Legacy error handling switch based on the JError::$legacy switch.
		// @deprecated  12.1
		if (JError::$legacy && $this->_db->getErrorNum())
		{
			$e = new JException($this->_db->getErrorMsg());
			$this->setError($e);
			return false;
		}

		// Check that we have a result.
		if (empty($row))
		{            
return false; //Panikos            
			$e = new JException(JText::_('JLIB_DATABASE_ERROR_EMPTY_ROW_RETURNED'));
			$this->setError($e);
			return false;
		}

		// Bind the object with the row and return.
		return $this->bind($row);
	}
        
}