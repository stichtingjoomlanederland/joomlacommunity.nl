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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

/**
 * PWT Sitemap Item Interface
 *
 * @since  1.0.0
 */
abstract class BasePwtSitemapItem
{
	/**
	 * Sitemap item title
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $title = '';

	/**
	 * Sitemap item link
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $link = '';

	/**
	 * Sitemap item level
	 *
	 * @var    integer
	 * @since  1.0.0
	 */
	public $level = 0;

	/**
	 * Sitemap item modified date
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $modified = '';

	/**
	 * Context of the sitemap item (ex: com_content.article.1)
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $context = '';

	/**
	 * Type of the sitemap item (placeholder or link)
	 *
	 * @var    string
	 * @since  1.0.1
	 */
	public $type = '';

	/**
	 * Set if it is an external URL
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	public $external = false;

	/**
	 * Constructor.
	 *
	 * @param   string   $title     Title
	 * @param   string   $link      URL
	 * @param   int      $level     Level
	 * @param   mixed    $modified  Modification date
	 * @param   boolean  $external  Internal or External URL
	 *
	 * @since   1.0.0
	 */
	public function __construct($title, $link, $level, $modified = null, $external = false)
	{
		$this->title    = $title;
		$this->link     = PwtSitemapUrlHelper::getURL($link);
		$this->level    = $level;
		$this->type     = $link ? 'link' : 'placeholder';
		$this->external = $external;

		// Check if this is an external link
		if (strpos($this->link, substr(Uri::base(), 0, -1)) === false)
		{
			$this->external = true;
		}

		// Set modified date
		if (!empty($modified) && $modified !== '1001-01-01 00:00' && $modified !== JDatabaseDriver::getInstance()->getNullDate())
		{
			$this->modified = HTMLHelper::_('date', $modified, 'Y-m-d');
		}
	}

	/**
	 * Render this item for a XML sitemap
	 *
	 * @return  string  Rendered sitemap item
	 *
	 * @since   1.0.0
	 */
	abstract public function renderXml();
}
