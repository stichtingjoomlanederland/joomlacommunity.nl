<?php
/**
 * @package     Perfect_Sitemap
 * @subpackage  com_perfectsitemap
 *
 * @copyright   Copyright (C) 2017 Perfect Web Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * HTML View class for Perfect Sitemap
 *
 * @since  1.0.0
 */
class PerfectSitemapViewSitemap extends JViewLegacy
{
	/**
	 * Sitemap items
	 *
	 * @var  array
	 */
	protected $items;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   1.0.0
	 */
	public function display($tpl = null)
	{
		// Get some data from the models
		$this->items = $this->get('Items');
		$this->state = $this->get('State');

		// get information from the menu
		$this->params = $this->state->get('parameters.menu', new Registry);

		if ($this->params->get('page_title'))
		{
			JFactory::getDocument()->setTitle($this->params->get('page_title'));
		}

		return parent::display($tpl);
	}
}
