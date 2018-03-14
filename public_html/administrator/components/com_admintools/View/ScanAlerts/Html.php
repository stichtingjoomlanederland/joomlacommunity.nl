<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\ScanAlerts;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Model\ScanAlerts;
use Akeeba\AdminTools\Admin\Model\Scans;
use FOF30\View\DataView\Html as BaseView;
use FOF30\Date\Date;
use JLoader;
use JText;

class Html extends BaseView
{
	/**
	 * The start date/time of the scan
	 *
	 * @var  Date
	 */
	public $scanDate;

	/**
	 * Should I be generating diffs for changed files?
	 *
	 * @var  bool
	 */
	public $generateDiff;

	/**
	 * The scanned file entry itself
	 *
	 * @var  ScanAlerts
	 */
	public $item;

	/**
	 * Threat index (high, medium, low, nonw)
	 *
	 * @var  string
	 */
	public $threatindex = 'high';

	/**
	 * File status
	 *
	 * @var  string
	 */
	public $fstatus = 'modified';

	/**
	 * Is this file suspicious?
	 *
	 * @var  bool
	 */
	public $suspiciousFile = false;

	/**
	 * The return array from Akeeba Engine while the scan is in progress
	 *
	 * @var  array
	 */
	public $retarray;

	/** @var  string	Order column */
	public $order;

	/** @var  string Order direction, ASC/DESC */
	public $order_Dir;

	/** @var  array	Sorting order options */
	public $sortFields = [];

	public $filters = [];

	protected function onBeforeEdit()
	{
		JLoader::import('joomla.utilities.date');
		JLoader::import('joomla.filesystem.file');

		// Load highlight.js
		$this->addJavascriptFile('//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/highlight.min.js');
		$this->addCssFile('//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/styles/default.min.css');

		$js = <<< JS

;;

akeeba.jQuery(document).ready(function($){
	hljs.initHighlightingOnLoad();
});

JS;

		$this->addJavascriptInline($js);

		$this->generateDiff = $this->container->params->get('scandiffs', false);

		parent::onBeforeEdit();

		/** @var Scans $scanModel */
		$scanModel = $this->container->factory->model('Scans')->tmpInstance();
		$scanModel->find($this->item->scan_id);

		$this->scanDate = new Date($scanModel->backupstart);
		$timezone       = $this->container->platform->getUser()->getParam('timezone', $this->container->platform->getConfig()->get('offset', 'GMT'));
		$tz             = new \DateTimeZone($timezone);
		$this->scanDate->setTimezone($tz);

		$this->item->newfile    = empty($this->item->diff);
		$this->item->suspicious = substr($this->item->diff, 0, 21) == '###SUSPICIOUS FILE###';

		// Calculate the threat index
		if ($this->item->threat_score == 0)
		{
			$this->threatindex = 'none';
		}
		elseif ($this->item->threat_score < 10)
		{
			$this->threatindex = 'low';
		}
		elseif ($this->item->threat_score < 100)
		{
			$this->threatindex = 'medium';
		}

		// File status
		if ($this->item->newfile)
		{
			$this->fstatus = 'new';
		}
		elseif ($this->item->suspicious)
		{
			$this->fstatus = 'suspicious';
		}

		// Should I render a diff?
		if (!empty($this->item->diff))
		{
			$diffLines = explode("\n", $this->item->diff);
			$firstLine = array_shift($diffLines);

			if ($firstLine == '###SUSPICIOUS FILE###')
			{
				$this->suspiciousFile = true;
				$this->item->diff     = '';
			}
			elseif ($firstLine == '###MODIFIED FILE###')
			{
				$this->item->diff = '';
			}

			if ($this->suspiciousFile && (count($diffLines) > 4))
			{
				array_shift($diffLines);
				array_shift($diffLines);
				array_shift($diffLines);
				array_shift($diffLines);

				$this->item->diff = implode("\n", $diffLines);
			}

			unset($diffLines);
		}

		// Should I enable tabs?
		if ($this->generateDiff && ($this->fstatus == 'modified'))
		{
			$this->addJavascriptFile('media://fef/js/tabs.min.js');

			$script = <<< JS

; // Working around broken 3PD plugins
akeeba.jQuery(document).ready(function($){
	akeeba.fef.tabs();
});
JS;
			$this->addJavascriptInline($script);
		}
	}

	protected function onBeforeBrowse()
	{
		parent::onBeforeBrowse();

		$hash = 'admintools'.$this->getName();

		// ...ordering
		$platform        = $this->container->platform;
		$input           = $this->input;
		$this->order     = $platform->getUserStateFromRequest($hash . 'filter_order', 'filter_order', $input, 'admintools_scanalert_id');
		$this->order_Dir = $platform->getUserStateFromRequest($hash . 'filter_order_Dir', 'filter_order_Dir', $input, 'DESC');

		// ...filter state
		$this->filters['status'] 	   = $platform->getUserStateFromRequest($hash . 'filter_status', 'status', $input);
		$this->filters['path'] 	 	   = $platform->getUserStateFromRequest($hash . 'filter_path', 'path', $input);
		$this->filters['acknowledged'] = $platform->getUserStateFromRequest($hash . 'filter_acknowledged', 'acknowledged', $input);

		// Construct the array of sorting fields
		$this->sortFields = array(
			'admintools_scanalert_id' => 'ID',
			'path' 					  => JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_PATH'),
			'filestatus' 			  => JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_STATUS'),
			'threat_score' 			  => JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_THREAT_SCORE'),
			'acknowledged'			  => JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_ACKNOWLEDGED'),
		);

		if ($this->getLayout() == 'print')
		{
			$this->onBeforePrint();
		}
	}

	public function onBeforePrint()
	{
		$script = <<<JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
(function($){
	$(document).ready(function(){
		if (window.print) {
			window.print();
		}
	});
})(akeeba.jQuery);

JS;
		$this->addJavascriptInline($script);
	}
}
