<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\View\Log;

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Backup\Admin\Model\Log;
use Akeeba\Backup\Admin\View\ViewTraits\ProfileIdAndName;
use Akeeba\Engine\Platform;
use FOF30\View\DataView\Html as BaseView;
use JHtml;

/**
 * View controller for the Log Viewer page
 */
class Html extends BaseView
{
	use ProfileIdAndName;

	/**
	 * JHtml list of available log files
	 *
	 * @var  array
	 */
	public $logs = [];

	/**
	 * Currently selected log file tag
	 *
	 * @var  string
	 */
	public $tag;

	/**
	 * The main page of the log viewer. It allows you to select a profile to display. When you do it displays the IFRAME
	 * with the actual log content and the button to download the raw log file.
	 *
	 * @return  void
	 */
	public function onBeforeMain()
	{
		// Get a list of log names
		/** @var Log $model */
		$model      = $this->getModel();
		$this->logs = $model->getLogList();

		$tag = $model->getState('tag');

		if (empty($tag))
		{
			$tag = null;
		}

		$this->tag = $tag;

		$this->getProfileIdAndName();

		JHtml::_('formbehavior.chosen');
	}
}