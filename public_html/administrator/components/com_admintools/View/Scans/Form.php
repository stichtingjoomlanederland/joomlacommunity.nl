<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\Scans;

defined('_JEXEC') or die;

use FOF30\View\DataView\Form as BaseView;
use JText;

class Form extends BaseView
{
	protected function onBeforeBrowse()
	{
		$msg = JText::_('COM_ADMINTOOLS_MSG_SCAN_LASTSERVERRESPONSE');
		$urlStart = 'index.php?option=com_admintools&view=Scans&task=startscan&format=raw';
		$urlStep = 'index.php?option=com_admintools&view=Scans&task=stepscan&format=raw';

		$script = <<<JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
admintools_scan_msg_ago = '$msg';
admintools_scan_ajax_url_start='$urlStart';
admintools_scan_ajax_url_step='$urlStep';

JS;
		$this->addJavascriptInline($script);

		parent::onBeforeBrowse();
	}
}