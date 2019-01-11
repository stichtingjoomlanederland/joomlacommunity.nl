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

require_once JPATH_SITE . '/components/com_tags/helpers/route.php';

/**
 * PWT Sitemap Tag Plugin
 *
 * @since  1.0.0
 */
class PlgPwtSitemapTag extends PwtSitemapPlugin
{
	/**
	 * Populate the PWT Sitemap plugin to use it a base class
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function populateSitemapPlugin()
	{
		$this->component = 'com_tags';
		$this->views     = ['tag'];
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
		// Register class here because register at the beginning of the file causes conflicts with the back-end model
		JLoader::register('TagsModelTag', JPATH_SITE . '/components/com_tags/models/tag.php');

		$sitemap_items = [];

		if ($this->checkDisplayParameters($item, $format))
		{
			$tags = $this->getTaggedItems($item->query['id'], $item->language, $item->params->get('include_children', 0));

			if ($tags !== false)
			{
				foreach ($tags as $tag)
				{
					$link = JRoute::_(TagsHelperRoute::getItemRoute($tag->content_item_id, $tag->core_alias, $tag->core_catid, $tag->core_language, $tag->type_alias, $tag->router));

					$sitemap_items[] = new PwtSitemapItem($tag->core_title, $link, $item->level + 1);
				}
			}
		}

		return $sitemap_items;
	}

	/**
	 * Get all tagged items
	 *
	 * @param   array  $aId             Tag ids
	 * @param   string $language        Language id
	 * @param   bool   $include_subtags Include subtags
	 *
	 * @return  stdClass
	 *
	 * @since   1.0.0
	 */
	private function getTaggedItems($aId, $language, $include_subtags)
	{
		$tagModel = new TagsModelTag();

		// Calling getState before setState will prevent 'populateState' override the new state
		$tagModel->getState();
		$tagModel->setState('tag.id', implode(',', $aId));

		// Set Tag Model parameters
		$tagModelParams                   = new stdClass();
		$tagModelParams->include_children = $include_subtags;
		$tagModel->setState('params', new \Joomla\Registry\Registry($tagModelParams));

		if ($language != '*')
		{
			$tagModel->setState('tag.language', $language);
		}

		return $tagModel->getItems();
	}
}