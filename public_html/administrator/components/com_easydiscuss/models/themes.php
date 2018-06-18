<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/model.php');

class EasyDiscussModelThemes extends EasyDiscussAdminModel
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Retrieves a list of installed themes on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getThemes()
	{
		$path = DISCUSS_THEMES;

		$result	= JFolder::folders($path, '.', false, true, $exclude = array('.svn', 'CVS', '.', '.DS_Store'));
		
		$themes	= array();

		// Cleanup output
		foreach ($result as $item) {
			$name = basename($item);
			$obj = ED::getThemeObject($name);

			if ($obj) {
				$obj->featured = false;

				if ($this->config->get('layout_site_theme') == $obj->element) {
					$obj->featured = true;
				}

				$themes[]	= $obj;
			}
		}

		return $themes;
	}

	/**
	 * Retrieves the current site template
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function getCurrentTemplate()
	{
		$db = ED::db();

		$query = 'SELECT ' . $db->qn('template') . ' FROM ' . $db->qn('#__template_styles');
		$query .= ' WHERE ' . $db->qn('home') . '!=' . $db->Quote(0);
		$query .= ' AND ' . $db->qn('client_id') . '=' . $db->Quote(0);

		$db->setQuery($query);

		$template = $db->loadResult();

		return $template;
	}

	/**
	 * Retrieves the current site template's path
	 *
	 * @since	3.1.1
	 * @access	public
	 */
	public function getCustomCssTemplatePath()
	{
		// Get the custom.css override path for the current Joomla template
		$template = $this->getCurrentTemplate();

		$path = JPATH_ROOT . '/templates/' . $template . '/html/com_easydiscuss/css/custom.css';

		return $path;
	}
}