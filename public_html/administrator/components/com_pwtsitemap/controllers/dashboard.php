<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\Component\Menus\Administrator\Table\MenuTable;

/**
 * PWT Sitemap item controller
 *
 * @since  1.3.0
 */
class PwtSitemapControllerDashboard extends FormController
{
	/**
	 * Save the new menu items
	 *
	 * @return void
	 *
	 * @since  1.3.0
	 *
	 * @throws Exception
	 */
	public function saveSitemapMenuItem()
	{
		$app = Factory::getApplication();
		$this->setRedirect(Route::_('index.php?option=com_pwtsitemap&view=dashboard'));

		// Check if we have a menu type
		if (!$this->input->get('menutype'))
		{
			$app->enqueueMessage(Text::_('COM_PWTSITEMAP_DASHBOARD_CONTROLLER_MISSING_MENU_ID'), 'error');

			return;
		}

		$component = ComponentHelper::getComponent('com_pwtsitemap');

		/** @var MenuTable $menuTable */
		$menuTable = Table::getInstance('Menu');

		$alias = $this->input->get('alias');

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__menu'))
			->where($db->quoteName('alias') . ' = ' . $db->quote($alias));
		$db->setQuery($query);

		if (count($db->loadResult()))
		{
			$alias .= mt_rand(null, 1000);
		}

		$data = [
			'menutype'     => $this->input->get('menutype'),
			'title'        => $this->input->get('sitemap'),
			'alias'        => $alias,
			'path'         => $alias,
			'link'         => $this->getMenuLink($this->input->get('type')),
			'type'         => 'component',
			'component_id' => $component->id,
			'language'     => '*',
			'published'    => 1
		];

		$menuTable->setLocation(1, 'last-child');

		try
		{
			$menuTable->save($data);

			$app->enqueueMessage(Text::_('COM_PWTSITEMAP_DASHBOARD_CONTROLLER_SITEMAP_SAVE_SUCCESS'));
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Get link by type for menu item
	 *
	 * @param   string  $type  The url to get
	 *
	 * @return  string The menu link for the given type
	 *
	 * @since   1.3.0
	 */
	private function getMenuLink($type)
	{
		$link = '';

		switch ($type)
		{
			case 'sitemap':
				$link = 'index.php?option=com_pwtsitemap&view=sitemap';
				break;
			case 'xmlitemap':
				$link = 'index.php?option=com_pwtsitemap&view=sitemap&layout=sitemapxml&format=xml';
				break;
			case 'multilingualsitemap':
				$link = 'index.php?option=com_pwtsitemap&view=multilanguage&format=xml';
				break;
			case 'imagesitemap':
				$link = 'index.php?option=com_pwtsitemap&view=image&format=xml';
				break;
		}

		return $link;
	}

	/**
	 * Add the Sitemap(s) to the robots.txt
	 *
	 * @return  void
	 * @since   1.3.0
	 * @throws  Exception
	 */
	public function addToRobots()
	{
		try
		{
			/** @var PwtSitemapModelDashboard $model */
			$model = $this->getModel('Dashboard');
			$model->addToRobots();
			$this->setRedirect(
				Route::_('index.php?option=com_pwtsitemap&view=dashboard'),
				Text::_('COM_PWTSITEMAP_ADD_TO_ROBOTS_SUCCESS'),
				'success'
			);
			$this->redirect();
		}
		catch (Exception $exception)
		{
			$this->setRedirect(
				Route::_('index.php?option=com_pwtsitemap&view=dashboard'),
				Text::_('COM_PWTSITEMAP_ADD_TO_ROBOTS_ERROR'),
				'error'
			);
			$this->redirect();
		}
	}
}
