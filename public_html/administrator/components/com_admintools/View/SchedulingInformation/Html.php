<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\SchedulingInformation;

defined('_JEXEC') || die;

use Akeeba\AdminTools\Admin\Model\SchedulingInformation;
use FOF40\View\DataView\Html as BaseView;

class Html extends BaseView
{
	/**
	 * Info about scheduling
	 *
	 * @var  object
	 */
	public $croninfo;

	protected function onBeforeMain()
	{
		/** @var SchedulingInformation $model */
		$model = $this->getModel();

		// Get the CRON paths
		$this->croninfo = $model->getPaths();
	}
}
