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

		// Empty alias
		$alias = '';

		// Get Slug
		$slug = isset($route['segments'][1]) ? $route['segments'][1] : '';

		// Check for JUG
		if (strpos($slug, 'joomla-gebruikersgroep-') !== false)
		{
			// Get menu alias
			$slugarray = explode(':', $slug, 2);
			$alias     = str_replace('joomla-gebruikersgroep-', '', $slugarray[1]);
		}

		// Check for overige
		if (strpos($slug, 'overige-joomla') !== false)
		{
			// Get menu alias
			$alias = 'overige';
		}

		// Check for cursussen
		if (strpos($slug, 'cursussen') !== false)
		{
			// Get menu alias
			$alias = 'cursussen';
		}

		// Check for pizza-bugs-fun
		if (strpos($slug, 'pizza-bugs-fun') !== false)
		{
			// Get menu alias
			$alias = 'pizza-bugs-fun';
		}

		// Check for pizza-bugs-fun
		if (strpos($slug, 'dutch-joomla-developers') !== false)
		{
			// Get menu alias
			$alias = 'dutch-joomla-developers';
		}

		if ($alias)
		{
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
			$route['segments']        = array();
		}

		return $route;
	}

	/**
	 * @param $data
	 *
	 *
	 * @since version
	 */
	public function rsepro_beforeEventStore($data)
	{
		$categories = $this->app->input->get('categories', array(), 'array');
		$db         = JFactory::getDbo();

		// Get category slug
		$query = $db->getQuery(true);
		$query->select('alias');
		$query->from($db->quoteName('#__categories'));
		$query->where($db->quoteName('id') . " = " . $db->quote($categories[0]));
		$db->setQuery($query);
		$alias = $db->loadResult();

		// Get ItemId
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from($db->quoteName('#__menu'));
		$query->where($db->quoteName('alias') . " = " . $db->quote($alias));
		$db->setQuery($query);
		$itemid = $db->loadResult();

		// Set Itemid
		$data['data']->set('itemid', $itemid);
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
