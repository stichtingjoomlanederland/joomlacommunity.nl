<?php
/**
 * @package     Perfect_Sitemap
 * @subpackage  plg_perfectsitemap
 *
 * @copyright   Copyright (C) 2017 Perfect Web Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Perfect Sitemap plugin
 *
 * @since  1.0.0
 */
class PlgSystemPerfectSitemap extends JPlugin
{
	/**
	 * Automatic load plugin language files
	 *
	 * @var bool
	 */
	protected $autoloadLanguage = true;

	/**
	 * Joomla Application instance
	 *
	 * @var  JApplicationSite
	 */
	public $app;

	/**
	 * Load perfectistemap plugin group and register helpers and classes
	 *
	 * @return  void
	 *
	 * @since  2.0.0
	 */
	public function onAfterInitialise()
	{
		JPluginHelper::importPlugin('perfectsitemap');

		JLoader::register('PerfectSitemapUrlHelper', JPATH_ROOT . '/components/com_perfectsitemap/helpers/urlhelper.php');
		JLoader::register('PerfectSitemapItem', JPATH_ROOT . '/components/com_perfectsitemap/helpers/perfectsitemapitem.php');
	}

	/**
	 * Add sitemap parameter to the menu edit form
	 *
	 * @param   JForm $form The form to be altered.
	 * @param   mixed $data The associated data for the form.
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function onContentPrepareForm($form, $data)
	{
		// Make sure form element is a JForm object
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return false;
		}

		// Make sure we are on the edit menu item page
		if (!in_array($form->getName(), array('com_menus.item')))
		{
			return true;
		}

		// Load form.xml
		JForm::addFormPath(__DIR__ . '/forms');
		$form->loadFile('perfectsitemap');

		return true;
	}
}
