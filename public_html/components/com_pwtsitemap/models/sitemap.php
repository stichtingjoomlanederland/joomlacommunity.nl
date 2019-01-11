<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\MenuItem;
use Joomla\CMS\MVC\Model\ItemModel;

defined('_JEXEC') or die;

/**
 * PWT Sitemap Component Model
 *
 * @since  1.0.0
 */
class PwtSitemapModelSitemap extends ItemModel
{
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
	protected $format;

	/**
	 * Type of the sitemap that is generated
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type;

	/**
	 * List of menu items
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	private $items = array();

	/**
	 * Constructor
	 *
	 * @param   array  $config  An array of configuration options (name, state, dbo, table_path, ignore_request).
	 *
	 * @throws  Exception
	 *
	 * @since  1.0.0
	 */
	public function __construct(array $config = array())
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
					array(
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
					)
				)
			)
			->select(
				$db->quoteName(
					array(
						'menu.path',
						'extensions.element',
						'menu_types.title'
					),
					array(
						'route',
						'component',
						'menuTitle'
					)
				)
			)
			->from($db->quoteName('#__menu','menu'))
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

		$this->items = $db->loadObjectList('id','Joomla\\CMS\\Menu\\MenuItem');

		foreach ($this->items as &$item)
		{
			// Get parent information.
			$parent_tree = array();

			if (isset($this->items[$item->parent_id]))
			{
				$parent_tree = $this->items[$item->parent_id]->tree;
			}

			// Create tree.
			$parent_tree[] = $item->id;
			$item->tree    = $parent_tree;

			// Create the query array.
			$url = str_replace('index.php?', '', $item->link);
			$url = str_replace('&amp;', '&', $url);

			parse_str($url, $item->query);
		}

		// Group all menu items based on their parent
		$groupedItems = array();

		foreach ($this->items as $item)
		{
			if (!isset($groupedItems[$item->menuTitle]))
			{
				$groupedItems[$item->menuTitle] = array();
			}

			$groupedItems[$item->menuTitle][] = $item;
		}

		return $groupedItems;
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
		$skipped_items = array();

		// Get menu items
		$groupedMenuItems = $this->getMenu();

		// Filter menu items and add articles
		foreach ($groupedMenuItems as $group => $menuItems)
		{
			// allow for plugins to change the menu items
			$this->jDispatcher->trigger('onPwtSitemapBeforeBuild', array(&$menuItems, $this->type, $this->format));

			foreach ($menuItems as $menuitem)
			{
				// Filter menu items
				if ($this->filter($menuitem))
				{
					$skipped_items[] = $menuitem->id;

					continue;
				}

				// Filter menu items we don't want to show for the display format and items where the parent is skipped
				if ($menuitem->params->get('addto' . $this->format . 'sitemap', 1) == false || in_array($menuitem->parent_id, $skipped_items))
				{
					$skipped_items[] = $menuitem->id;
					continue;
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
						break;

					default:
						$menuitem->link = null;
						break;
				}

				// Get the PWT Sitemap settings
				$menuitem->addtohtmlsitemap = $menuitem->params->get('addtohtmlsitemap', 1);
				$menuitem->addtoxmlsitemap  = $menuitem->params->get('addtoxmlsitemap', 1);

				// Trigger plugin event
				$this->jDispatcher->trigger('onPwtSitemapAddMenuItemToSitemap', array($menuitem));

				// Add item to sitemap
				if (!$menuitem->doNotAdd)
				{
					$this->AddMenuItemToSitemap($menuitem, $group);
				}

				// Trigger plugin event
				$results = $this->jDispatcher->trigger('onPwtSitemapBuildSitemap', array($menuitem, $this->format, $this->type));

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
		$this->jDispatcher->trigger('onPwtSitemapAfterBuild', array(&$this->sitemap->sitemapItems, $menuItems, $this->type));

		return $this->sitemap;
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
	protected function AddMenuItemToSitemap($menuitem, $group)
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
	protected function AddItemsToSitemap($items, $group)
	{
		$this->sitemap->addItem($items, $group);
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

		return (($menuitem->language != $lang->getTag() and $menuitem->language != '*')
			|| !in_array($menuitem->access, $authorizedAccessLevels)
		);
	}
}
