<?php
/**
 * @package   AdminTools
* Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\SecurityExceptions;

defined('_JEXEC') or die;

use FOF30\Model\DataModel;
use FOF30\View\DataView\Html as BaseView;
use JText;

class Html extends BaseView
{
	/** @var  string	Order column */
	public $order = 'id';

	/** @var  string Order direction, ASC/DESC */
	public $order_Dir = 'DESC';

	/** @var  array	Sorting order options */
	public $sortFields = [];

	public $filters = [];

	protected function onBeforeBrowse()
	{
		/**
		 * Set the correct ordering for this view.
		 */

		/** @var DataModel $model */
		$model = $this->getModel();
		$model->savestate(1);

		$order = $model->getState('filter_order', 'logdate', 'cmd');
		$dir   = $model->getState('filter_order_Dir', 'DESC', 'cmd');

		parent::onBeforeBrowse();

		$hash = 'admintoolslogs';

		$platform        = $this->container->platform;
		$input           = $this->input;
		$this->order     = $order;
		$this->order_Dir = strtolower($dir);

		// ...filter state
		$this->filters['from'] 	 	  = $platform->getUserStateFromRequest($hash . 'filter_from', 'datefrom', $input);
		$this->filters['to'] 	 	  = $platform->getUserStateFromRequest($hash . 'filter_to', 'dateto', $input);
		$this->filters['ip'] 	 	  = $platform->getUserStateFromRequest($hash . 'filter_ip', 'ip', $input);
		$this->filters['reason']	  = $platform->getUserStateFromRequest($hash . 'filter_reason', 'reason', $input);

		// Construct the array of sorting fields
		$this->sortFields = array(
			'logdate' 		=> JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_LOGDATE'),
			'ip' 	 		=> JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_IP'),
			'reason'	 	=> JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_REASON'),
			'url'	 		=> JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_URL')
		);
	}
}
