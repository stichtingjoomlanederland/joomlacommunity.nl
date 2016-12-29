<?php
/**
 * @package     JoomlaCommunity
 * @subpackage  plg_system_joomlacommunity
 *
 * @copyright   Copyright (C) 2016 Sander Potjer, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JoomlaCommunity System plugin
 *
 * @since  1.0.0
 */
class PlgSystemJoomlaCommunity extends JPlugin
{
	/**
	 * @var    JFactory::getApplication();
	 * @since  1.0.0
	 */
	protected $app;

	/**
	 * Extend the RSEvents buildRoute
	 *
	 * @return bool
	 *
	 * @since  1.0.0
	 */
	public function rsepro_buildRoute($route)
	{
		// Only continue if view is not set
		if (isset($route['query']['view']))
		{
			return true;
		}

		// Get Slug
		$slug = isset($route['segments'][1]) ? $route['segments'][1] : '';

		// Check for JUG
		if (strpos($slug, 'joomla-gebruikersgroep-') !== false)
		{
			// Get menu alias
			$slugarray = explode(':', $slug, 2);
			$alias     = str_replace('joomla-gebruikersgroep-', '', $slugarray[1]);

			// Get ItemId
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from($db->quoteName('#__menu'));
			$query->where($db->quoteName('alias') . " = " . $db->quote($alias));
			$db->setQuery($query);
			$itemid = $db->loadResult();

			// Modify route data
			$route['query']['Itemid'] = $itemid;
			$route['segments']        = '';
		}

		return $route;
	}

	/**
	 * Event method onAfterRender
	 *
	 * @return bool
	 */
	public function onAfterRender()
	{
		$body = $this->app->getBody();

		if ($this->app->isAdmin())
		{
			return false;
		}

		// Replace joomlacommunity.eu by joomlacommunity.nl
		$body = str_replace('http://www.joomlacommunity.eu', 'https://www.joomlacommunity.nl', $body);

		$this->app->setBody($body);

		return true;
	}
}
