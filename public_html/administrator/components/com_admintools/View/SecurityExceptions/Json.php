<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\SecurityExceptions;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Model\SecurityExceptions;
use FOF30\View\DataView\Json as BaseView;

class Json extends BaseView
{
	public function onBeforeBrowse($tpl = null)
	{
		$this->alreadyLoaded = true;

		/** @var SecurityExceptions $model */
		$model = $this->getModel();

		$this->limitStart = $model->getState('limitstart', 0);
		$this->limit = $model->getState('limit', 0);
		$this->items = $model->getRawDataArray($this->limitStart, $this->limit, true);
		$this->total = count($this->items);

		parent::onBeforeBrowse();
	}
}