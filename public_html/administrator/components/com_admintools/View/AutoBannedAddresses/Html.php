<?php
/**
 * @package   AdminTools
* Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\AutoBannedAddresses;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\View\Mixin\SystemPluginExists;
use FOF30\View\DataView\Html as BaseView;
use JText;

class Html extends BaseView
{
	use SystemPluginExists;

	/** @var  string	Order column */
	public $order;

	/** @var  string Order direction, ASC/DESC */
	public $order_Dir = 'DESC';

	/** @var  array	Sorting order options */
	public $sortFields = [];

	public $filters = [];

	protected function onBeforeBrowse()
	{
		$this->populateSystemPluginExists();

		parent::onBeforeBrowse();

		$hash = 'admintools'.strtolower($this->getName());

		// ...ordering
		$platform        = $this->container->platform;
		$input           = $this->input;
		$this->order     = $platform->getUserStateFromRequest($hash . 'filter_order', 'filter_order', $input, 'ip');
		$this->order_Dir = $platform->getUserStateFromRequest($hash . 'filter_order_Dir', 'filter_order_Dir', $input, 'DESC');

		// ...filter state
		$this->filters['ip'] 	 	  = $platform->getUserStateFromRequest($hash . 'filter_ip', 'ip', $input);

		// Construct the array of sorting fields
		$this->sortFields = array(
			'ip' 	 	=> JText::_('COM_ADMINTOOLS_LBL_AUTOBANNEDADDRESS_IP'),
			'reason'	=> JText::_('COM_ADMINTOOLS_LBL_AUTOBANNEDADDRESS_REASON'),
			'until' 	=> JText::_('COM_ADMINTOOLS_LBL_AUTOBANNEDADDRESS_UNTIL'),
		);
	}
}
