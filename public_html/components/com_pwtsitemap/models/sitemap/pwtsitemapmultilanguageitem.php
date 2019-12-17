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

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

JLoader::register('BasePwtSitemapItem', JPATH_ROOT . '/components/com_pwtsitemap/models/sitemap/basepwtsitemapitem.php');

/**
 * PWT Sitemap Item object
 *
 * @since  1.0.0
 */
class PwtMultilanguageSitemapItem extends BasePwtSitemapItem
{
	/**
	 * Associated items
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	public $associations = [];

	/**
	 * Render this item for a XML sitemap
	 *
	 * @return  string  Rendered sitemap item
	 *
	 * @since   1.0.0
	 */
	public function renderXML()
	{
		// Get the optional language code settings from plugin
		$languagecode = PluginHelper::getPlugin('system', 'languagecode');
		$params       = new Registry($languagecode->params);

		$item = '<url><loc>' . htmlspecialchars($this->link) . '</loc>';

		if ($this->modified)
		{
			$item .= '<lastmod>' . $this->modified . '</lastmod>';
		}

		if ($this->associations && is_array($this->associations))
		{
			foreach ($this->associations as $language => $association)
			{
				$item .= '
				 <xhtml:link
					rel="alternate"
					hreflang="' . $params->get(strtolower($language), $language) . '"
					href="' . PwtSitemapUrlHelper::getURL('index.php?Itemid=' . $association->id . '&lang=' . $language) . '"
	            />';
			}
		}

		$item .= '</url>';

		return $item;
	}
}
