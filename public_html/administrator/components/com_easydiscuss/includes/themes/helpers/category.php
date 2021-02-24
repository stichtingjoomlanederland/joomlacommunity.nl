<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussThemesHelperCategory
{
	/**
	 * Generates the category avatar / icon / color
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function identifier($category, $size = 'sm', $borderSize = '', $wrapperClasses = '')
	{
		$config = ED::config();

		$namespace = 'avatar';

		if ($config->get('layout_category_icons')) {
			$namespace = 'icon';
		}

		$namespace = 'identifier.' . $namespace;

		// Avatar sizes
		$size = 'o-avatar--' . $size;

		// Avatar borders
		$border = '';
		
		if ($borderSize) {
			$border = 'o-avatar--border--' . $borderSize;
		}


		$theme = ED::themes();
		$theme->set('size', $size);
		$theme->set('wrapperClasses', $wrapperClasses);
		$theme->set('border', $border);
		$theme->set('category', $category);
		$output = $theme->output('site/helpers/category/' . $namespace);

		return $output;
	}

	/**
	 * Generates the name of the category
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function title(EasyDiscussCategory $category, $options = [])
	{
		static $cache = [];

		if (!isset($cache[$category->id])) {
			$popbox = ED::normalize($options, 'popbox', true);
			$customClass = ED::normalize($options, 'customClass', '');


			$theme = ED::themes();
			$theme->set('popbox', $popbox);
			$theme->set('category', $category);
			$theme->set('customClass', $customClass);

			$cache[$category->id] = $theme->output('site/helpers/category/title');
		}

		return $cache[$category->id];
	}
}
