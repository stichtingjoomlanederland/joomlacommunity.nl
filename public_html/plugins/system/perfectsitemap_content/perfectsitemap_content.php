<?php
/**
 * @package     Perfect_Sitemap
 * @subpackage  plg_perfectsitemap
 *
 * @copyright   Copyright (C) 2016 Perfect Web Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('URLHelper', JPATH_ROOT . '/components/com_perfectsitemap/helpers/urlhelper.php');

/**
 * Perfect Sitemap plugin
 *
 * @since  1.0.0
 */
class PlgSystemPerfectSitemap_Content extends JPlugin
{
	// Autoload language
	protected $autoloadLanguage = true;

	// JFactory::getApplication()
	public $app;

	/**
	 * Adds additional fields to the user editing form
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

		// Load selected option and view if selected
		if (isset($data['request']['view']) && isset($data['request']['option']))
		{
			$view   = $data['request']['view'];
			$option = $data['request']['option'];

			if ($option == 'com_content' && $view == 'category')
			{
				JForm::addFormPath(__DIR__ . '/forms');
				$form->loadFile('perfectsitemap_content');
			}
		}

		return true;
	}

	/**
	 * Run for every menuitem passed by Perfect Sitemap
	 *
	 * @param   stdClass  $item  Joomla! menu item
	 *
	 * @return  array
	 *
	 * @since   1.4.0
	 */
	public function onPerfectSitemapBuildSitemap($item)
	{
		$sitemap_items = null;

		if (isset($item->query['option']) && isset($item->query['view']) && $item->params->get('addarticletohtmlsitemap', 1))
		{
			if ($item->query['option'] == 'com_content' && $item->query['view'] == 'category')
			{
				// Save new items
				$sitemap_items = array();

				// Get database connection
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);

				// Build query
				$query->select($db->quoteName(array('id', 'title', 'alias', 'catid', 'language')));
				$query->from($db->quoteName('#__content'));
				$query->where($db->quoteName('access') . ' = ' . $db->quote('1'));
				$query->where($db->quoteName('catid') . ' = ' . $db->quote($item->query['id']));
				$query->where($db->quoteName('language') . ' = ' . $db->quote($item->language));
				$query->where($db->quoteName('state') . ' = 1');

				// Send query
				$db->setQuery($query);

				// Get results
				$articles = $db->loadObjectList();

				// Add article to sitemap_items
				foreach ($articles as $article)
				{
					$tmpitem        = new stdClass;
					$tmpitem->title = $article->title;
					$tmpitem->link  = URLHelper::getURL('index.php?option=com_content&view=article&id=' . $article->id . ':' . $article->alias . '&catid=' . $article->catid . '&Itemid=' . $item->id);
					$tmpitem->level = $item->level + 1;

					$sitemap_items[] = $tmpitem;
				}
			}
		}

		return $sitemap_items;
	}
}
