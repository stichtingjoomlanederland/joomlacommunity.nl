<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\Scans;

defined('_JEXEC') || die;

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
		$platform        = $this->container->platform;

		$msg      = Text::_('COM_ADMINTOOLS_MSG_SCAN_LASTSERVERRESPONSE');
		$urlStart = 'index.php?option=com_admintools&view=Scans&task=startscan&format=raw';
		$urlStep  = 'index.php?option=com_admintools&view=Scans&task=stepscan&format=raw';

		$platform->addScriptOptions('admintools.Scan.lastResponseMessage', $msg);
		$platform->addScriptOptions('admintools.Scan.urlStart', $urlStart);
		$platform->addScriptOptions('admintools.Scan.urlStep', $urlStep);

		parent::onBeforeBrowse();

		$hash = 'admintools' . $this->getName();

		// ...ordering
		$input           = $this->input;
		$this->order     = $platform->getUserStateFromRequest($hash . 'filter_order', 'filter_order', $input, 'id');
		$this->order_Dir = $platform->getUserStateFromRequest($hash . 'filter_order_Dir', 'filter_order_Dir', $input, 'DESC');

		// Construct the array of sorting fields
		$this->sortFields = [
			'id'        => '#',
			'scanstart' => Text::_('COM_ADMINTOOLS_LBL_SCAN_START'),
		];

		$this->addJavascriptFile('admin://components/com_admintools/media/js/scan.min.js', $this->container->mediaVersion, 'text/javascript', true);
	}
}
