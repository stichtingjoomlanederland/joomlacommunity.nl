<?php
/**
 * @package   AdminTools
 * @copyright 2010-2018 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\Scans;

defined('_JEXEC') or die;

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
		$msg 	  = JText::_('COM_ADMINTOOLS_MSG_SCAN_LASTSERVERRESPONSE');
		$urlStart = 'index.php?option=com_admintools&view=Scans&task=startscan&format=raw';
		$urlStep  = 'index.php?option=com_admintools&view=Scans&task=stepscan&format=raw';

		$script = <<<JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
admintools_scan_msg_ago = '$msg';
admintools_scan_ajax_url_start='$urlStart';
admintools_scan_ajax_url_step='$urlStep';

JS;
		$this->addJavascriptInline($script);

		parent::onBeforeBrowse();

		$hash = 'admintools'.$this->getName();

		// ...ordering
		$platform        = $this->container->platform;
		$input           = $this->input;
		$this->order     = $platform->getUserStateFromRequest($hash . 'filter_order', 'filter_order', $input, 'id');
		$this->order_Dir = $platform->getUserStateFromRequest($hash . 'filter_order_Dir', 'filter_order_Dir', $input, 'DESC');

		// Construct the array of sorting fields
		$this->sortFields = array(
			'id' => '#',
			'backupstart' => JText::_('COM_ADMINTOOLS_LBL_SCAN_START'),
		);

		$this->addJavascriptFile('admin://components/com_admintools/media/js/Modal.min.js');
		$this->addJavascriptFile('admin://components/com_admintools/media/js/scan.min.js');
	}
}
