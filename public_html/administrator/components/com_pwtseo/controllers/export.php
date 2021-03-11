<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;

defined('_JEXEC') or die;

/**
 * Exports controller class
 *
 * @since  1.3.0
 */
class PWTSEOControllerExport extends FormController
{
	/**
	 * Export the articles. Closes the APP so the csv is forced to be donwloaded
	 *
	 * @since  1.3.0
	 */
	public function articles()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$now = $db->quote(Factory::getDate()->toSql());

		$query
			->select(
				$db->quoteName(
					array(
						'content.id',
						'seo.serptitle',
						'seo.serpurl',
						'seo.serpmetadescription',
						'seo.focus_word',
						'seo.pwtseo_score'
					)
				)
			)
			->from($db->quoteName('#__content', 'content'))
			->where($db->quoteName('access') . ' = 1')
			->where($db->quoteName('state') . ' = 1')
			->where('(' . $db->quoteName('publish_down') . ' < ' . $now . ' OR ' . $db->quoteName('publish_down') . ' = ' . $db->quote($db->getNullDate()) . ')')
			->where($db->quoteName('publish_up') . ' <' . $now);

		$query
			->leftJoin($db->quoteName('#__plg_pwtseo', 'seo') . ' ON ' . $db->quoteName('seo.context_id') . ' = ' . $db->quoteName('content.id') .
				'AND ' . $db->quoteName('seo.context') . ' = ' . $db->quote('com_content.article')
			);

		$list = $db->setQuery($query, 0, 0)->loadObjectList();

		// Export the entire list to csv and offer for download
		$headers = array(
			Text::_('COM_PWTSEO_EXPORT_ID'),
			Text::_('COM_PWTSEO_EXPORT_TITLE'),
			Text::_('COM_PWTSEO_EXPORT_URL'),
			Text::_('COM_PWTSEO_EXPORT_METADESCRIPTION'),
			Text::_('COM_PWTSEO_EXPORT_FOCUSWORD'),
			Text::_('COM_PWTSEO_EXPORT_SCORE')
		);

		// Clean up any Joomla! stuff, we don't need it
		ob_end_clean();

		// Set headers so browser will download
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=content-' . (new \Joomla\CMS\Date\Date)->toISO8601() . '.csv');

		$out = fopen('php://output', 'w');
		ob_start();

		fputcsv($out, $headers);

		foreach ($list as $item)
		{
			fputcsv($out, (array) $item);
		}

		ob_end_flush();

		Factory::$application->close();
	}

	/**
	 * Export the menu items. Closes the APP so the csv is forced to be donwloaded
	 *
	 * @since  1.3.0
	 */
	public function menuitems()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select(
				$db->quoteName(
					array(
						'menu.id',
						'seo.serptitle',
						'seo.serpurl',
						'seo.serpmetadescription',
						'seo.focus_word',
						'seo.pwtseo_score',
					)
				)
			)
			->from($db->quoteName('#__menu', 'menu'))
			->leftJoin($db->quoteName('#__plg_pwtseo', 'seo') . ' ON seo.context_id = menu.id')
			->where($db->quoteName('menu.client_id') . ' = 0')
			->where($db->quoteName('seo.context_id') . ' NOT IN (' . implode(',', $db->quote(array('com_content.article', 'com_pwtseo.custom'))) . ')')
			->where($db->quoteName('menu.alias') . ' <> ' . $db->quote('root'));

		$list = $db->setQuery($query, 0, 0)->loadObjectList();

		// Export the entire list to csv and offer for download
		$headers = array(
			Text::_('COM_PWTSEO_EXPORT_ID'),
			Text::_('COM_PWTSEO_EXPORT_TITLE'),
			Text::_('COM_PWTSEO_EXPORT_URL'),
			Text::_('COM_PWTSEO_EXPORT_METADESCRIPTION'),
			Text::_('COM_PWTSEO_EXPORT_FOCUSWORD'),
			Text::_('COM_PWTSEO_EXPORT_SCORE')
		);

		// Clean up any Joomla! stuff, we don't need it
		ob_end_clean();

		// Set headers so browser will download
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=menu-' . (new \Joomla\CMS\Date\Date)->toISO8601() . '.csv');

		$out = fopen('php://output', 'w');
		ob_start();

		fputcsv($out, $headers);

		foreach ($list as $item)
		{
			fputcsv($out, (array) $item);
		}

		ob_end_flush();

		Factory::$application->close();
	}

	/**
	 * Export the custom url's. Closes the APP so the csv is forced to be donwloaded
	 *
	 * @since  1.3.0
	 */
	public function custom()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$now = $db->quote(Factory::getDate()->toSql());

		$query
			->select(
				$db->quoteName(
					array(
						'seo.serptitle',
						'seo.serpurl',
						'seo.serpmetadescription',
						'seo.focus_word',
						'seo.pwtseo_score'
					)
				)
			)
			->from($db->quoteName('#__plg_pwtseo', 'seo'))
			->where($db->quoteName('context') . ' = ' . $db->quote('com_pwtseo.custom'));

		$list = $db->setQuery($query, 0, 0)->loadObjectList();

		// Export the entire list to csv and offer for download
		$headers = array(
			Text::_('COM_PWTSEO_EXPORT_TITLE'),
			Text::_('COM_PWTSEO_EXPORT_URL'),
			Text::_('COM_PWTSEO_EXPORT_METADESCRIPTION'),
			Text::_('COM_PWTSEO_EXPORT_FOCUSWORD'),
			Text::_('COM_PWTSEO_EXPORT_SCORE')
		);

		// Clean up any Joomla! stuff, we don't need it
		ob_end_clean();

		// Set headers so browser will download
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=seo-' . (new \Joomla\CMS\Date\Date)->toISO8601() . '.csv');

		$out = fopen('php://output', 'w');
		ob_start();

		fputcsv($out, $headers);

		foreach ($list as $item)
		{
			fputcsv($out, (array) $item);
		}

		ob_end_flush();

		Factory::$application->close();
	}
}
