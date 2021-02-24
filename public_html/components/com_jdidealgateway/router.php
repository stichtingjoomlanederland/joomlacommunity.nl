<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterBase;
use Joomla\CMS\Factory;

/**
 * Routing class.
 *
 * @package  JDiDEAL
 * @since    4.3.1
 */
class JdidealgatewayRouter extends RouterBase
{
	/**
	 * Build the route for the com_jdidealgateway component.
	 *
	 * @param   array  &$query  An array of URL arguments.
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   4.3.1
	 */
	public function build(&$query)
	{
		// Initialize variables.
		$segments = [];

		// Check for view
		if (!isset($query['view']) && !isset($query['Itemid']))
		{
			// No view and no Itemid is gonna be hard ;)
			return $segments;
		}

		if (!isset($query['view']))
		{
			// Get the view from the Itemid.
			$link = Factory::getApplication()->getMenu()->getItem(
				$query['Itemid']
			)->link;

			$matches = array();
			preg_match("/view=([a-z,A-z,0-9]*)/", $link, $matches);

			$view = count($matches) ? $matches[1] : null;
		}
		else
		{
			// Easy mode
			$view = $query['view'];
			unset($query['view']);
		}

		$language = Factory::getLanguage()->getTag();

		if (isset($query['lang']))
		{
			$language = $query['lang'];
			unset($query['lang']);
		}

		switch ($view)
		{
			case 'pay':
				if (isset($query['task']))
				{
					switch ($query['task'])
					{
						case 'pay.result':
							$segments[] = 'result';
							break;
						case 'pay.sendmoney':
							$segments[] = 'send';
							break;
					}

					unset($query['task']);
				}

				$profileId = $query['profile_id'] ?? null;
				unset($query['profile_id']);

				if (isset($query['Itemid']))
				{
					break;
				}

				$query['Itemid'] = $this->getItemid(
					$view, $language, null, $profileId
				);

				// If no menu item is found, re-set the original view
				if (!$query['Itemid'])
				{
					$query['view'] = $view;
				}

				$payFields = [
					'amount',
					'email',
					'remark',
					'number',
					'silent',
				];

				// Collect the values
				$values = [];

				foreach ($payFields as $name)
				{
					if (isset($query[$name]))
					{
						$values[$name] = $query[$name];
						unset($query[$name]);
					}
				}

				unset($query['task'], $query['profile_id']);

				if ($values)
				{
					$segments[] = base64_encode(json_encode($values));
				}
				break;
			case 'checkout':
			case 'status':
				$query['Itemid'] = $this->getItemid($view, $language);

				// If no menu item is found, re-set the original view
				if (!$query['Itemid'])
				{
					$query['view'] = $view;
				}
				break;
		}

		return $segments;
	}

	/**
	 * Find the item ID for a given view.
	 *
	 * @param   string  $view       The name of the view to find the item ID for
	 * @param   string  $language   The language to use for finding menu items
	 * @param   null    $id         The id of an item
	 * @param   null    $profileId  The profile ID linked to a menu item
	 *
	 * @return  mixed  The item ID or null if not found.
	 *
	 * @since   4.3.1
	 */
	private function getItemid(string $view, string $language, $id = null,
		$profileId = null
	) {
		$items = $this->menu->getItems(
			[
				'component',
				'language',
			],
			[
				'com_jdidealgateway',
				$language,
			]
		);

		if ($language !== '*')
		{
			$items = array_merge(
				$items, $this->menu->getItems(
				[
					'component',
					'language',
				],
				[
					'com_jdidealgateway',
					'*',
				]
			)
			);
		}

		$itemId = null;

		if ($id)
		{
			foreach ($items as $item)
			{
				if (isset($item->query['view'], $item->query['id'])
					&& $item->query['view'] === $view
					&& (int) $item->query['id'] === (int) $id)
				{
					$itemId = $item->id;
					break;
				}
			}
		}

		if ($profileId)
		{
			foreach ($items as $item)
			{
				if (isset($item->query['view'], $item->query['profile_id'])
					&& $item->query['view'] === $view
					&& (int) $item->query['profile_id'] === (int) $profileId)
				{
					$itemId = $item->id;
					break;
				}
			}
		}

		if (!$itemId)
		{
			foreach ($items as $item)
			{
				if (isset($item->query['view'])
					&& $item->query['view'] === $view)
				{
					$itemId = $query['Itemid'] = $item->id;
					break;
				}
			}
		}

		return $itemId;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  $segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   4.3.1
	 */
	public function parse(&$segments)
	{
		$vars = [];

		// Get the view
		$menu = $this->menu->getActive();

		// Get the view from the menu
		if ($menu)
		{
			$view = $menu->query['view'];
		}

		// If there is no menu item, get the view from the URL
		if (empty($view))
		{
			$view = $this->app->input->get('view');
		}

		switch ($view)
		{
			case 'pay':
				switch ($segments[0])
				{
					case 'result':
						$vars['task'] = 'pay.result';
						break;
					case 'send':
						$vars['task'] = 'pay.sendmoney';
						break;
				}

				if (isset($segments[1]))
				{
					$values = json_decode(base64_decode($segments[1]), true);

					if (is_array($values))
					{
						$vars = array_merge($vars, $values);
					}
				}

				$vars['view'] = 'pay';
				break;
		}

		return $vars;
	}
}
