<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * Profiles table.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class TableProfile extends Table
{
	/**
	 * Constructor.
	 *
	 * @param   JDatabaseDriver  $db  A database connector object.
	 *
	 * @since   4.0.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__jdidealgateway_profiles', 'id', $db);
	}

	/**
	 * Check that everything is OK.
	 *
	 * @return  boolean  True if all checks are OK | False if there is an issue.
	 *
	 * @since   4.2.0
	 *
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 */
	public function check(): bool
	{
		// Check that the alias is unique
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName($this->_tbl))
			->where($db->quoteName('alias') . ' = ' . $db->quote($this->get('alias')));
		$db->setQuery($query);

		$profileId = $db->loadResult();

		if ($profileId > 0 && $profileId !== $this->get('id'))
		{
			throw new InvalidArgumentException(Text::_('COM_ROPAYMENTS_ALIAS_EXISTS'));
		}

		return true;
	}
}
