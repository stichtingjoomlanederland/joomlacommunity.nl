<?php
/**
 * @package     Perfect_Sitemap
 * @subpackage  com_perfectsitemap
 *
 * @copyright   Copyright (C) 2016 Perfect Web Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML View class for Perfect Sitemap
 *
 * @since  1.0.0
 */
class PerfectSitemapViewSitemap extends JViewLegacy
{
	protected $items;

	public function display($tpl = 'xml')
	{
		// Get some data from the models
		$items        = $this->get('Items');
		$this->items  = $items;

		return parent::display($tpl);
	}
}
