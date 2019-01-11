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
	public $associations;

	/**
	 * Render this item for a XML sitemap
	 *
	 * @return  string  Rendered sitemap item
	 *
	 * @since   1.0.0
	 */
	public function renderXML()
	{
		$item = '<url><loc>' . $this->link . '</loc>';

		if ($this->modified != null)
		{
			$item .= '<lastmod>' . $this->modified . '</lastmod>';
		}

		if ($this->associations != null)
		{
			foreach ($this->associations as $language => $association)
			{
				$item .= '
				 <xhtml:link
					rel="alternate"
					hreflang="' . $language . '"
					href="' . PwtSitemapUrlHelper::getURL($association->link . '&lang=' . $language) . '"
	            />';
			}
		}

		$item .= '</url>';

		return $item;
	}
}