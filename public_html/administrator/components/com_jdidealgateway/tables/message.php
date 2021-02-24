<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * Message table.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class TableMessage extends Table
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
		parent::__construct('#__jdidealgateway_messages', 'id', $db);
	}

	/**
	 * Overrides Table::store to set modified data and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   4.1.0
	 */
	public function store($updateNulls = false)
	{
		$date = Factory::getDate();
		$user = Factory::getUser();

		$this->set('modified', $date->toSql());

		if ($this->get('id', false))
		{
			// Existing item
			$this->set('modified_by', $user->get('id'));
		}
		else
		{
			$this->set('created', $date->toSql());

			if (empty($this->get('created_by')))
			{
				$this->set('created_by', $user->get('id'));
			}
		}

		return parent::store($updateNulls);
	}
}
