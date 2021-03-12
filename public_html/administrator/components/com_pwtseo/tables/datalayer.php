<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * Datalayer table
 *
 * @since  1.3.0
 */
class PWTSEOTableDatalayer extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver $db Database driver object.
	 *
	 * @since   1.1.0
	 */
	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__plg_pwtseo_datalayers', 'id', $db);
	}

	/**
	 * Method to perform sanity checks on the Table instance properties to ensure they are safe to store in the database.
	 *
	 * Child classes should override this method to make sure the data they are storing in the database is safe and as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @since   1.7.0
	 */
	public function check()
	{
		if (trim($this->name) == '')
		{
			$this->name = $this->title;
			$this->name = str_replace('-', '_', ApplicationHelper::stringURLSafe($this->name, $this->language));
		}

		$this->template = implode(',', $this->template);

		return true;
	}

	/**
	 * Overloaded load to prepare the template field for a Form.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.
	 *                           If not set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 *
	 * @since   1.5.0
	 *
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function load($keys = null, $reset = true)
	{
		$result = parent::load($keys, $reset);

		if ($result)
		{
			$this->template = json_encode(explode(',', $this->template));
		}

		return $result;
	}
}
