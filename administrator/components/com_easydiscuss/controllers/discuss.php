<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class EasyDiscussControllerDiscuss extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Allows caller to clear the css and js cache
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function compileStylesheet()
	{
		// Get a list of themes first
		$path = DISCUSS_MEDIA . '/themes';

		// Get a list of themes
		$themes = JFolder::folders($path, '.', false, true);

		foreach ($themes as $theme) {
			// Get the cache folder and clear it
			$cachePath = $theme . '/less/cache';

			// Delete the theme's cache folder
			JFolder::delete($cachePath);

			// Re-create an empty cache folder
			JFolder::create($cachePath);

			// Get the theme's name
			$themeName = basename($theme);

			// Recompile the theme's less file now
			$stylesheet = ED::stylesheet();

			// For now, admin theme is hardcoded
			if ($themeName == 'admin') {
				$stylesheet->compileAdminStylesheet();
			} else {
				$stylesheet->compileSiteStylesheet($themeName);
			}
		}

		ED::setMessage('COM_EASYDISCUSS_STYLESHEET_STYLESHEET_PURGED', 'success');
		return $this->app->redirect('index.php?option=com_easydiscuss');
	}


	/**
	 * Allows caller to clear the resources cache
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function clearCache()
	{
		// Get list of files inside /media/com_easydiscuss/resources
		$resources = DISCUSS_MEDIA . '/resources/';

		$files = JFolder::files($resources);

		foreach ($files as $file) {
			$state = JFile::delete($resources . $file);
		}

		ED::setMessage('COM_EASYDISCUSS_STYLESHEET_CACHE_PURGED', 'success');
		return $this->app->redirect('index.php?option=com_easydiscuss');
	}
}
