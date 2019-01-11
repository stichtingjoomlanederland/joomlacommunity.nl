<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_contact/helpers/route.php';
require_once JPATH_ROOT . '/components/com_contact/models/category.php';
require_once JPATH_ROOT . '/components/com_contact/models/featured.php';

/**
 * PWT Sitemap Contact
 *
 * @since  1.0.0
 */
class PlgPwtSitemapContact extends PwtSitemapPlugin
{
	/**
	 * Populate the PWT sitemap plugin to use it a base class
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function populateSitemapPlugin()
	{
		$this->component = 'com_contact';
		$this->views     = ['category', 'featured'];
	}

	/**
	 * Run for every menuitem passed
	 *
	 * @param   StdClass $item   Menu items
	 * @param   string   $format Sitemap format that is rendered
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function onPwtSitemapBuildSitemap($item, $format)
	{
		$sitemap_items = [];

		if ($this->checkDisplayParameters($item, $format))
		{
			if ($item->query['view'] == 'featured')
			{
				$contacts = $this->getFeaturedContacts();
			}
			else
			{
				$contacts = $this->getContacts($item->query['id']);
			}


			if ($contacts !== false)
			{
				foreach ($contacts as $contact)
				{
					$link = ContactHelperroute::getContactRoute($contact->id . ':' . $contact->alias, $contact->catid, $contact->language);

					$sitemap_items[] = new PwtSitemapItem($contact->name, $link, $item->level + 1);
				}
			}
		}

		return $sitemap_items;
	}

	/**
	 * Get all contacts from a category
	 *
	 * @param   int $id Category id
	 *
	 * @return  mixed  stdClass on success, false otherwise
	 *
	 * @since   1.0.0
	 */
	private function getContacts($id)
	{
		$contactModel = new ContactModelCategory();

		// Calling getState before setState will prevent 'populateState' override the new state
		$contactModel->getState();
		$contactModel->setState('category.id', $id);

		return $contactModel->getItems();
	}

	/**
	 * Get all featured contacts
	 *
	 * @return  mixed  stdClass on success, false otherwise
	 *
	 * @since   1.0.0
	 */
	private function getFeaturedContacts()
	{
		return JModelLegacy::getInstance('ContactModelFeatured')->getItems();
	}
}