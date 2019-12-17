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

JLoader::register('BasePwtSitemapItem', JPATH_ROOT . '/components/com_pwtsitemap/models/sitemap/basepwtsitemapitem.php');

/**
 * PWT Sitemap Item object
 *
 * @since  1.0.0
 */
class PwtSitemapImageItem extends BasePwtSitemapItem
{
	/**
	 * Images relevant to this item
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	public $images = [];

	/**
	 * Render this item for a XML sitemap
	 *
	 * @return  string  Rendered sitemap item
	 *
	 * @since   1.0.0
	 */
	public function renderXML()
	{
		$item = '<url><loc>' . htmlspecialchars($this->link) . '</loc>';

		if ($this->images && is_array($this->images))
		{
			foreach ($this->images as $image)
			{
				if (!empty($image->caption))
				{
					$item .= '
					<image:image>
						<image:loc>' . htmlspecialchars($image->url) . '</image:loc>
						<image:caption>' . htmlspecialchars($image->caption) . '</image:caption>
	                </image:image>';
				}
				else
				{
					$item .= '
					<image:image>
						<image:loc>' . htmlspecialchars($image->url) . '</image:loc>
	                </image:image>';
				}
			}
		}

		$item .= '</url>';

		return $item;
	}
}
