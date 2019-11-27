<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * PWT Image Profiles table.
 *
 * @package  Pwtimage
 * @since    1.1.0
 */
class TableProfile extends Table
{
	/**
	 * Constructor.
	 *
	 * @param   JDatabaseDriver  $db  A database connector object.
	 *
	 * @since   4.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__pwtimage_profiles', 'id', $db);
	}
}
