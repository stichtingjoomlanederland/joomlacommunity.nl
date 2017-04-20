<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewComponents extends JViewLegacy
{
	protected $items;
	protected $filterbar;
	protected $pagination;

	public function display($tpl = null) {
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->filterbar 	= $this->get('FilterBar');
		
		$this->state		= $this->get('State');
		$this->search		= $this->state->get('components.filter.search');

		parent::display($tpl);
	}
}