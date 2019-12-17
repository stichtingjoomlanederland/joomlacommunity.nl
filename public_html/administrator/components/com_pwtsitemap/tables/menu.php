<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Table\Table;

/**
 * MenuTypes table
 *
 * @since  1.3.0
 */
class PwtSitemapTableMenu extends Table
{
	/**
	 * Constructor
	 *
	 * @param   \JDatabaseDriver  $db  Database driver object.
	 *
	 * @since  1.3.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__pwtsitemap_menu_types', 'menu_types_id', $db);
	}
}
