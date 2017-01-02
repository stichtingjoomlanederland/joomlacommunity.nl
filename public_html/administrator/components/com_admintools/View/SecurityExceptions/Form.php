<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\SecurityExceptions;

defined('_JEXEC') or die;

use FOF30\Model\DataModel;
use FOF30\View\DataView\Form as BaseView;

class Form extends BaseView
{
	protected function onBeforeBrowse()
	{
		/**
		 * Set the correct ordering for this view.
		 */

		/** @var DataModel $model */
		$model = $this->getModel();
		$model->savestate(1);
		$order = $model->getState('filter_order', 'logdate', 'cmd');
		$dir = $model->getState('filter_order_Dir', 'DESC', 'cmd');

		parent::onBeforeBrowse();

		$this->lists->order = $order;
		$this->lists->order_Dir = strtolower($dir);
	}

}