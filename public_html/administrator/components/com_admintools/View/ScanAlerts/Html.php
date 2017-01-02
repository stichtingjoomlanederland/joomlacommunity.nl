<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\ScanAlerts;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Model\ScanAlerts;
use Akeeba\AdminTools\Admin\Model\Scans;
use FOF30\View\DataView\Html as BaseView;
use JDate;
use JLoader;

class Html extends BaseView
{
	/**
	 * The start date/time of the scan
	 *
	 * @var  JDate
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

		$this->scanDate = new JDate($scanModel->backupstart);

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
	}

	protected function onBeforeBrowse()
	{
		parent::onBeforeBrowse();

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