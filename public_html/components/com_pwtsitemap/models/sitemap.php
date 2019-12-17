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

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\MenuItem;
use Joomla\CMS\MVC\Model\ItemModel;

/**
 * PWT Sitemap Component Model
 *
 * @since  1.0.0
 */
class PwtSitemapModelSitemap extends ItemModel
{
	/**
	 * PWT sitemap object instance
	 *
	 * @var    PwtSitemap
	 * @since  1.0.0
	 */
	protected $sitemap;

	/**
	 * Display format
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $format = '';

	/**
	 * Type of the sitemap that is generated
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type = '';

	/**
	 * JApplication instance
	 *
	 * @var    CMSApplication
	 * @since  1.0.0
	 */
	private $app;

	/**
	 * Holds the dispatcher object
	 *
	 * @var    JEventDispatcher
	 * @since  1.0.0
	 */
	private $jDispatcher;

	/**
	 * List of menu items
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	private $items = [];

	/**
	 * Constructor
	 *
	 * @param   array  $config  An array of configuration options (name, state, dbo, table_path, ignore_request).
	 *
	 * @since  1.0.0
	 *
	 * @throws  Exception
	 */
	public function __construct($config = [])
	{
		$this->app         = Factory::getApplication();
		$this->jDispatcher = JEventDispatcher::getInstance();

		$this->format  = $this->app->input->getCmd('format', 'html');
		$this->type    = 'default';
		$this->sitemap = new PwtSitemap($this->format);

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 * @since   1.0.0
	 */
	public function populateState()
	{
		parent::populateState();

		$params = $this->app->getParams();
		$this->setState('params', $params);
	}

	/**
	 * Build the sitemap
	 *
	 * @return  PwtSitemap
	 *
	 * @since   1.0.0
	 */
	public function getSitemap()
	{
		$skippedItems = [];

		// Get menu items
		$groupedMenuItems = $this->getMenu();

		// Filter menu items and add articles
		foreach ($groupedMenuItems as $group => $menuItems)
		{
			// Allow for plugins to change the menu items
			$this->jDispatcher->trigger('onPwtSitemapBeforeBuild', [&$menuItems, $this->type, $this->format]);

			foreach ($menuItems as $menuitem)
			{
				// Filter menu items
				if ($this->filter($menuitem))
				{
					$skippedItems[] = $menuitem->id;

					continue;
				}

				
				// Filter menu items we don't want to show for the display format and items where the parent is skipped
				if ((int) $menuitem->params->get('addto' . $this->format . 'sitemap', 1) === 0
					|| in_array($menuitem->parent_id, $skippedItems, true))
				{
					if ((int) $menuitem->params->get('addcontentto' . $this->format . 'sitemap', 0) === 0)
					{
						$skippedItems[] = $menuitem->id;
						continue;
					}

					$menuitem->doNotAdd = true;
				}
				

				// Generate link based on menu-item type
				switch ($menuitem->type)
				{
					case 'component':
						$menuitem->link = 'index.php?Itemid=' . $menuitem->id;
						break;

					case 'alias':
						$menuitem->link = 'index.php?Itemid=' . $menuitem->params->get('aliasoptions');
						break;

					case 'url':
						if (strpos($menuitem->link, 'http') !== false)
						{
							break;
						}

						if (strpos($menuitem->link, '/') !== 0)
						{
							$menuitem->link = '/' . $menuitem->link;
						}

						break;

					default:
						$menuitem->link = null;
						break;
				}

				// Get the PWT Sitemap settings
				$menuitem->addtohtmlsitemap = $menuitem->params->get('addtohtmlsitemap', 1);
				$menuitem->addtoxmlsitemap  = $menuitem->params->get('addtoxmlsitemap', 1);

				// Add item to sitemap
				if (!isset($menuitem->doNotAdd) || (isset($menuitem->doNotAdd) && !$menuitem->doNotAdd))
				{
					// Trigger plugin event
					$this->jDispatcher->trigger('onPwtSitemapAddMenuItemToSitemap', [$menuitem]);

					$this->addMenuItemToSitemap($menuitem, $group);
				}

				// Trigger plugin event
				$results = $this->jDispatcher->trigger('onPwtSitemapBuildSitemap',
					[$menuitem, $this->format, $this->type]
				);

				foreach ($results as $sitemapItems)
				{
					if (!empty($sitemapItems))
					{
						$this->addItemsToSitemap($sitemapItems, $group);
					}
				}
			}
		}

		// Allow for plugins to change the entire sitemap along with what was processed
		$this->jDispatcher->trigger('onPwtSitemapAfterBuild', [&$this->sitemap->sitemapItems, $menuItems, $this->type]);

		return $this->sitemap;
	}

	/**
	 * Get the menu items for the sitemap.
	 *
	 * @return  array  List of menu items.
	 *
	 * @since   1.2.0
	 */
	private function getMenu()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'menu.id',
						'menu.menutype',
						'menu.title',
						'menu.alias',
						'menu.note',
						'menu.link',
						'menu.type',
						'menu.level',
						'menu.language',
						'menu.browserNav',
						'menu.access',
						'menu.params',
						'menu.home',
						'menu.img',
						'menu.template_style_id',
						'menu.component_id',
						'menu.parent_id'
					]
				)
			)
			->select(
				$db->quoteName(
					[
						'menu.path',
						'extensions.element',
						'menu_types.title',
						'pwtsitemap_menu_types.custom_title'
					],
					[
						'route',
						'component',
						'menuTitle',
						'customTitle'
					]
				)
			)
			->from($db->quoteName('#__menu', 'menu'))
			->leftJoin(
				$db->quoteName('#__extensions', 'extensions')
				. ' ON ' . $db->quoteName('menu.component_id') . ' = ' . $db->quoteName('extensions.extension_id')
			)
			->leftJoin(
				$db->quoteName('#__menu_types', 'menu_types')
				. ' ON ' . $db->quoteName('menu_types.menutype') . ' = ' . $db->quoteName('menu.menutype')
			)
			->leftJoin(
				$db->quoteName('#__pwtsitemap_menu_types', 'pwtsitemap_menu_types')
				. ' ON ' . $db->quoteName('pwtsitemap_menu_types.menu_types_id') . ' = ' . $db->quoteName('menu_types.id')
			)
			->where($db->quoteName('menu.published') . ' = 1')
			->where($db->quoteName('menu.parent_id') . ' > 0')
			->where($db->quoteName('menu.client_id') . ' = 0')
			->order($db->quoteName('pwtsitemap_menu_types.ordering') . ' ASC')
			->order($db->quoteName('menu.lft'));

		// Set the query
		$db->setQuery($query);

		$this->items = $db->loadObjectList('id', MenuItem::class);

		foreach ($this->items as &$item)
		{
			// Get parent information.
			$parentTree = [];

			
			if (isset($this->items[$item->parent_id]))
			{
				$parentTree = $this->items[$item->parent_id]->tree;
			}
			

			// Create tree.
			$parentTree[] = $item->id;
			$item->tree   = $parentTree;

			// Create the query array.
			$url = str_replace(array('index.php?', '&amp;'), array('', '&'), $item->link);

			parse_str($url, $item->query);
		}

		// Group all menu items based on their parent
		$groupedItems = [];

		foreach ($this->items as $groupedItem)
		{
			if (isset($groupedItem->customTitle) && $groupedItem->customTitle && !isset($groupedItems[$groupedItem->customTitle]))
			{
				$groupedItems[$groupedItem->customTitle] = [];
			}
			elseif (isset($groupedItem->menuTitle) && !isset($groupedItems[$groupedItem->menuTitle]))
			{
				$groupedItems[$groupedItem->menuTitle] = [];
			}

			if (isset($groupedItem->customTitle) && $groupedItem->customTitle)
			{
				$groupedItems[$groupedItem->customTitle][] = $groupedItem;
			}
			else
			{
				$groupedItems[$groupedItem->menuTitle][] = $groupedItem;
			}
		}

		return $groupedItems;
	}

	/**
	 * Filter a menu item on content type, language and access
	 *
	 * @param   MenuItem  $menuitem  Menu item to filter
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	protected function filter($menuitem)
	{
		$lang                   = Factory::getLanguage();
		$authorizedAccessLevels = Factory::getUser()->getAuthorisedViewLevels();

		return (($menuitem->language !== $lang->getTag() && $menuitem->language !== '*')
			|| !in_array((int) $menuitem->access, $authorizedAccessLevels, true)
		);
	}

	/**
	 * Add a menu item to the sitemap
	 *
	 * @param   MenuItem  $menuitem  Menu item to add to the sitemap
	 * @param   string    $group     Set the group the item belongs to
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function addMenuItemToSitemap($menuitem, $group)
	{
		$this->sitemap->addItem(new PwtSitemapItem($menuitem->title, $menuitem->link, $menuitem->level), $group);
	}

	/**
	 * Add a array of PwtSitemapItems to the sitemap (used for the result of plugin triggers)
	 *
	 * @param   array   $items  Menu item to add to the sitemap
	 * @param   string  $group  Set the group the item belongs to
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function addItemsToSitemap($items, $group)
	{
		$this->sitemap->addItem($items, $group);
	}
}
