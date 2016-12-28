<?php
/**
 * @package     Perfect_Sitemap
 * @subpackage  plg_perfectsitemap
 *
 * @copyright   Copyright (C) 2016 Perfect Web Team. All rights reserved.
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
	// Autoload language
	protected $autoloadLanguage = true;

	// JFactory::getApplication()
	public $app;

	/**
	 * Add sitemap parameter to the menu edit form
	 *
	 * @param   JForm  $form  The form to be altered.
	 * @param   mixed  $data  The associated data for the form.
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
			$this->_subject->setError("JERROR_NOT_A_FORM");

			return false;
		}

		$name = $form->getName();

		// Make sure we are on the edit menu item page
		if (!in_array($name, array('com_menus.item')))
		{
			return true;
		}

		// Load form.xml
		JForm::addFormPath(__DIR__ . '/forms');
		$form->loadFile('perfectsitemap');

		return true;
	}
}
