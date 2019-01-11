<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\View\Alice;

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Backup\Admin\Model\Log;
use Akeeba\Engine\Platform;
use FOF30\View\DataView\Html as BaseView;
use JHtml;
use JText;

/**
 * View controller for the Backup Now page
 */
class Html extends BaseView
{
	/**
	 * List of log entries to choose from, JHtml compatible
	 *
	 * @var  array
	 */
	public $logs;

	/**
	 * Currently selected log
	 *
	 * @var  string
	 */
	public $log;

	/**
	 * Should I autostart the log analysis? 0/1
	 *
	 * @var  int
	 */
	public $autorun;

	public function onBeforeMain()
	{
		// Load the necessary Javascript
		$this->addJavascriptFile('media://com_akeeba/js/Stepper.min.js');
		$this->addJavascriptFile('media://com_akeeba/js/Alice.min.js');

		/** @var Log $logModel */
		$logModel = $this->container->factory->model('Log')->tmpInstance();

		// Get a list of log names
		$this->logs = $logModel->getLogList();
		$this->log  = $this->input->getCmd('log', null);

		JText::script('COM_AKEEBA_ALICE_SUCCESSS');
		JText::script('COM_AKEEBA_ALICE_WARNING');
		JText::script('COM_AKEEBA_ALICE_ERROR');
		JText::script('COM_AKEEBA_BACKUP_TEXT_LASTRESPONSE');
	}
}
