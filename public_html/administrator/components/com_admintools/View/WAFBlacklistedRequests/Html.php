<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\WAFBlacklistedRequests;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\View\Mixin\SystemPluginExists;
use FOF30\View\DataView\Html as BaseView;
use JText;

class Html extends BaseView
{
	use SystemPluginExists;

	/** @var  string	Order column */
	public $order = 'id';

	/** @var  string Order direction, ASC/DESC */
	public $order_Dir = 'DESC';

	/** @var  array	Sorting order options */
	public $sortFields = [];

	public $filters = [];

	protected function onBeforeBrowse()
	{
		$this->populateSystemPluginExists();

		$hash = 'admintoolswafblacklist';

		// ...ordering
		$platform        = $this->container->platform;
		$input           = $this->input;
		$this->order     = $platform->getUserStateFromRequest($hash . 'filter_order', 'filter_order', $input, 'id');
		$this->order_Dir = $platform->getUserStateFromRequest($hash . 'filter_order_Dir', 'filter_order_Dir', $input, 'DESC');

		// ...filter state
		$this->filters['application'] = $platform->getUserStateFromRequest($hash . 'filter_application', 'application', $input);
		$this->filters['fverb'] 	 = $platform->getUserStateFromRequest($hash . 'filter_fverb', 'fverb', $input);
		$this->filters['foption'] 	 = $platform->getUserStateFromRequest($hash . 'filter_foption', 'foption', $input);
		$this->filters['fview']   	 = $platform->getUserStateFromRequest($hash . 'filter_fview', 'fview', $input);
		$this->filters['ftask']   	 = $platform->getUserStateFromRequest($hash . 'filter_ftask', 'ftask', $input);
		$this->filters['fquery'] 	 = $platform->getUserStateFromRequest($hash . 'filter_fquery', 'fquery', $input);
		$this->filters['fquery_content'] = $platform->getUserStateFromRequest($hash . 'filter_fquery_content', 'fquery_content', $input);
		$this->filters['published']  = $platform->getUserStateFromRequest($hash . 'filter_enabled', 'enabled', $input);

		// Construct the array of sorting fields
		$this->sortFields = array(
			'id'        => JText::_('ID'),
			'foption' 	=> JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_OPTION'),
			'fview' 	=> JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_VIEW'),
			'fquery'  	=> JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY'),
		);

		parent::onBeforeBrowse();
	}
}
