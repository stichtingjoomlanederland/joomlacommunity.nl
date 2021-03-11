<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * Structured Data table
 *
 * @since  1.3.0
 */
class PWTSEOTableStructuredData extends Table
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
		parent::__construct('#__plg_pwtseo', 'id', $db);
	}
}
