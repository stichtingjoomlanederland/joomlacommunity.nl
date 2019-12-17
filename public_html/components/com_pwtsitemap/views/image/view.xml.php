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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;

/**
 * XML View class for PWT Sitemap
 *
 * @since  1.0.0
 */
class PwtSitemapViewImage extends HtmlView
{
	/**
	 * Sitemap
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $sitemap = [];

	/**
	 * sitemap items
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $items = [];

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = 'xml')
	{
		/** @var PwtSitemapModelImage $model */
		$model         = $this->getModel();
		$this->sitemap = $model->getSitemap();

		// Get the sitemap items with the given part
		$this->items = $this->sitemap->sitemapItems;

		if ($this->items === false)
		{
			Factory::getApplication()->setHeader('status', 404, true);

			return false;
		}

		return parent::display($tpl);
	}
}
