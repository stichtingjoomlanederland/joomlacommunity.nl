<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussThemesHelperTable
{
	/**
	 * Renders the checkall checkbox for each table heading
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function checkall()
	{
		$theme = ED::themes();

		$output = $theme->output('admin/html/table.checkall');

		return $output;
	}

	/**
	 * Renders an ordering column for table
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function ordering($name, $index, $totalItems, $allowed = false)
	{
		// Get the ordering key
		$theme = ED::themes();

		$theme->set('total', $totalItems);
		$theme->set('allowed', $allowed);
		$theme->set('index', $index);

		$output = $theme->output('admin/html/table.ordering');

		return $output;
	}

	/**
	 * Renders the publish button for each row in a table
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function publish($post, $index)
	{
		$theme = ED::themes();

		$theme->set('post', $post);
		$theme->set('index', $index);
		$output = $theme->output('admin/html/table.publish');

		return $output;
	}

	/**
	 * Renders the featured / unfeatured button for each row in a table
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function featured($view, $obj, $property = 'default', $tasks = 'toggleFeatured', $allowed = true)
	{
		$theme = ED::themes();

		$class = 'default';

		if ($obj->$property) {
			$class = 'featured';
		}

		$theme->set('class', $class);
		$theme->set('allowed', $allowed);
		$theme->set('task', $tasks);

		$output = $theme->output('admin/html/table.state');

		return $output;
	}

	/**
	 * Renders the checkbox for each row in a table
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function checkbox($index, $value, $allowed = true, $name = 'cid')
	{
		$theme = ED::themes();

		$theme->set('index', $index);
		$theme->set('value', $value);
		$theme->set('allowed', $allowed);
		$theme->set('name', $name);

		$output = $theme->output('admin/html/table.checkbox');

		return $output;
	}

	/**
	 * Renders the pagination's limit dropdown
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function limit($pagination)
	{
		$theme = ED::themes();
		$theme->set('pagination', $pagination);

		$output = $theme->output('admin/html/table.limit');

		return $output;
	}

	/**
	 * Renders a notice line on a table listing
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function notice($text, $icon = 'fa-info-circle', $class = 'alert-warning')
	{
		$theme = ED::themes();
		$theme->set('text', $text);
		$theme->set('icon', $icon);
		$theme->set('class', $class);

		$output = $theme->output('admin/html/table.notice');

		return $output;
	}

	/**
	 * Renders a dropdown for filtering resultset purpose
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function filter($name, $selected, $items = array())
	{
		$theme = ED::themes();

		// Determines if there is a default option by determining the empty key in $items
		$keys = array_keys($items);

		$initial = JText::_('COM_EASYDISCUSS_TABLE_FILTER');

		if (in_array('', $keys, true)) {
			$initial = JText::_($items['']);
		}

		// If there is no items, we set the default filters
		if (!$items) {
			$items = array('P' => 'COM_EASYDISCUSS_TABLE_FILTER_PUBLISHED', 'U' => 'COM_EASYDISCUSS_TABLE_FILTER_UNPUBLISHED');
		}

		$theme->set('initial', $initial);
		$theme->set('items', $items);
		$theme->set('name', $name);
		$theme->set('selected', $selected);

		$output = $theme->output('admin/html/table.filter');

		return $output;
	}

	/**
	 * Renders a searchbox for table listings
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function search($name, $search = '')
	{
		$theme = ED::themes();

		$theme->set('search', $search);
		$theme->set('name', $name);

		$output = $theme->output('admin/html/table.search');

		return $output;
	}

	/**
	 * Renders a state column
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function state($view, $obj, $property = '', $controller = '', $allowed = true, $tasks = array())
	{
		// If the property is not provided, we assume that it uses the property of `state` by default.
		if (!$property) {
			$property = 'state';
		}

		// If controller is empty, we assume it's the same name as the view
		if (!$controller) {
			$controller = $view;
		}

		// Task mapping
		$publish = isset($tasks[0]) ? $tasks[0] : 'publish';
		$unpublish = isset($tasks[1]) ? $tasks[1] : 'unpublish';
		$task = $publish;

		if ($obj->$property) {
			$task = $unpublish;
		}

		// Get classes definitions
		$classes = self::getViewStates($view);

		// Default values
		$class = $classes[$obj->$property];

		$theme = ED::themes();
		$theme->set('class', $class);
		$theme->set('allowed', $allowed);
		$theme->set('task', $task);

		return $theme->output('admin/html/table.state');
	}

	/**
	 * Returns the mapping of classes for states
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getViewStates($view)
	{
		static $states = array();

		if (!isset($states[$view])) {
			$obj = self::getStatesConfig();

			if (!isset($obj[$view])) {
				$states[$view] = $obj['default'];
			} else {
				$states[$view] = $obj[$view];
			}
		}

		return $states[$view];
	}

	public static function getStatesConfig()
	{
		$obj = array(
					'spools' => array('0' => 'scheduled', '1' => 'published'),

					'default' => array('0' => 'unpublished', "1" => "published")
				);

		return $obj;
	}
}
