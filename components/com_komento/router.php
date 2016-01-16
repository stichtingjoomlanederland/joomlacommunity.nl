<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'bootstrap.php' );

function KomentoBuildRoute( &$query )
{
	// Declare static variables.
	static $items;
	static $default;
	static $dashboard;
	static $profile;
	static $rss;

	// Initialise variables.
	$segments	= array();
	$konfig		= Komento::getKonfig();

	// Get the relevant menu items if not loaded.
	if (empty($items))
	{
		// Get all relevant menu items.
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$items	= $menu->getItems('component', 'com_komento');

		// Build an array of serialized query strings to menu item id mappings.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			// Check to see if we have found the dashboard menu item.
			if (empty($dashboard) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'dashboard'))
			{
				$dashboard = $items[$i]->id;
			}

			// Check to see if we have found the profile menu item.
			if (empty($profile) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'profile'))
			{
				$profile = $items[$i]->id;
			}

			// Check to see if we have found the registration menu item.
			if (empty($feed) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'rss'))
			{
				$rss = $items[$i]->id;
			}
		}

		// Set the default menu item to use for com_users if possible.
		// Do not set default because different item uses different menu. It wouldn't make sense if RSS link is using profile menu item to generate
		// if ($dashboard)
		// {
		// 	$default = $dashboard;
		// }
		// elseif ($profile)
		// {
		// 	$default = $profile;
		// }
		// elseif ($feed)
		// {
		// 	$default = $rss;
		// }
	}

	if (!empty($query['view']))
	{
		if( !isset( $query['Itemid'] ) )
		{
			// Set menu item directly with the view as the variable string
			// Profile link should be generated with $profile item id
			// If the view is 'profile', then itemid shouhld be set with $profile
			// If the view is 'rss', then the itemid should be set with $rss
			$query['Itemid'] = ${$query['view']};
			// $query['Itemid'] = $default;
		}

		// If itemid is empty, then append the view into segments
		// If itemid is not empty, then no need to append segments as the itemid title will be in the address
		if( empty( $query['Itemid'] ) )
		{
			$segments[] = $query['view'];
		}

		switch ($query['view'])
		{
			case 'rss':
				if ($query['Itemid'] == $rss)
				{
					unset ($query['view']);
				}
				break;
			case 'profile':
				if ($query['Itemid'] == $profile)
				{
					unset ($query['view']);
				}
				// Only append the user id if not "me".
				$user = JFactory::getUser();
				if (!empty($query['id']) && ($query['id'] != $user->id)) {
					$segments[] = $query['id'];
				}
				unset ($query['id']);

				break;
			default:
			case 'dashboard':
				if (!empty($query['view'])) {
					$segments[] = $query['view'];
				}
				unset ($query['view']);
				if ($query['Itemid'] == $dashboard) {
					unset ($query['view']);
				}
				break;
		}
	}

	return $segments;
}

function KomentoParseRoute( &$segments )
{
	// Initialise variables.
	$vars	= array();
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$count	= count($segments);

	// Only run routine if there are segments to parse.
	if( count($segments) < 1 )
	{
		return;
	}

	if (!isset($item))
	{
		$vars['view']	= $segments[0];
	}
	else
	{
		$vars['view']	= $item->query['view'];
	}

	if( $vars['view'] == 'profile' && $count > 0 )
	{
		// $userId		= array_pop($segments);
		// $user		= JFactory::getUser( $userId );
		// $vars['id']	= $user->id;

		$vars['id']	= array_pop($segments);
	}

	return $vars;
}

