<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\SecurityExceptions;

defined('_JEXEC') || die;

use Akeeba\AdminTools\Admin\Model\SecurityExceptions;
use FOF40\View\DataView\Json as BaseView;

class Json extends BaseView
{
	public function onBeforeBrowse($tpl = null)
	{
		$this->alreadyLoaded = true;

		/** @var SecurityExceptions $model */
		$model = $this->getModel();

		$this->limitStart = $model->getState('limitstart', 0);
		$this->limit      = $model->getState('limit', 0);
		$this->items      = $model->getRawDataArray($this->limitStart, $this->limit, true);
		$this->total      = is_array($this->items) || $this->items instanceof \Countable ? count($this->items) : 0;

		parent::onBeforeBrowse();
	}
}
