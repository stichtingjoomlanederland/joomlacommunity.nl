<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\SecurityExceptions;

defined('_JEXEC') || die;

use FOF40\Model\DataModel;
use FOF40\View\DataView\Html as BaseView;
use Joomla\CMS\Language\Text;

class Html extends BaseView
{
	/** @var  string    Order column */
	public $order = 'id';

	/** @var  string Order direction, ASC/DESC */
	public $order_Dir = 'DESC';

	/** @var  array    Sorting order options */
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
		$this->filters['from']   = $platform->getUserStateFromRequest($hash . 'filter_from', 'datefrom', $input);
		$this->filters['to']     = $platform->getUserStateFromRequest($hash . 'filter_to', 'dateto', $input);
		$this->filters['ip']     = $platform->getUserStateFromRequest($hash . 'filter_ip', 'ip', $input);
		$this->filters['reason'] = $platform->getUserStateFromRequest($hash . 'filter_reason', 'reason', $input);

		// Construct the array of sorting fields
		$this->sortFields = [
			'logdate' => Text::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_LOGDATE'),
			'ip'      => Text::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_IP'),
			'reason'  => Text::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_REASON'),
			'url'     => Text::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_URL'),
		];
	}
}
