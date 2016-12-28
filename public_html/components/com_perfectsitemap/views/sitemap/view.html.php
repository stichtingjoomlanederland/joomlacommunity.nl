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

	public function display($tpl = null)
	{
		// Get some data from the models
		$this->items  = $this->get('Items');

		$this->state = $this->get('State');
		
		// get information from the menu
		$this->params = $this->state->get('parameters.menu');

		if ($this->params->get('page_title'))
		{
			JFactory::getDocument()->setTitle($this->params->get('page_title'));
		}

		return parent::display($tpl);
	}
}
