<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * Logs table.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class TableLog extends Table
{
	/**
	 * Constructor.
	 *
	 * @param   JDatabaseDriver  $db  A database connector object.
	 *
	 * @since   2.0.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__jdidealgateway_logs', 'id', $db);
	}
}
