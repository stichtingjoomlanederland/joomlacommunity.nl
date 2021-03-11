<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\ScanAlerts;

defined('_JEXEC') || die;

use Akeeba\AdminTools\Admin\Model\ScanAlerts;
use Akeeba\AdminTools\Admin\Model\Scans;
use DateTimeZone;
use FOF40\Date\Date;
use FOF40\View\DataView\Html as BaseView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

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

	/** @var  string    Order column */
	public $order;

	/** @var  string Order direction, ASC/DESC */
	public $order_Dir;

	/** @var  array    Sorting order options */
	public $sortFields = [];

	public $filters = [];

	public function onBeforePrint()
	{
		$script = <<<JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
akeeba.Loader.add(['akeeba.System'], function () {
    akeeba.System.documentReady(function() {
        if (window.print) {
			window.print();
		}
    });
})

JS;
		$this->addJavascriptInline($script);
	}

	protected function onBeforeEdit()
	{
		// Load highlight.js
		$this->addJavascriptFile('//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.5.0/highlight.min.js', $this->container->mediaVersion, 'text/javascript', true);
		$this->addCssFile('//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.5.0/styles/default.min.css', $this->container->mediaVersion);
		$this->addCssFile('//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.5.0/styles/dracula.min.css', $this->container->mediaVersion, 'text/css', 'screen and (prefers-color-scheme: dark)');

		$js = <<< JS

akeeba.Loader.add(['akeeba.System', 'hljs'], function() {
    akeeba.System.documentReady(function() {
        akeeba.System.forEach(document.querySelectorAll('pre.highlightCode'), function(index, block) {
            hljs.highlightBlock(block);
        });
    });
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
		$tz             = new DateTimeZone($timezone);
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

 // Working around broken 3PD plugins
akeeba.Loader.add(['akeeba.fef.tabs'], function() {
    akeeba.fef.tabs();
});
JS;
			$this->addJavascriptInline($script);
		}
	}

	protected function onAfterEdit()
	{
		// Change the page subtitle
		$subtitle = Text::sprintf('COM_ADMINTOOLS_TITLE_SCANALERT_EDIT', $this->item->scan_id);
		ToolbarHelper::title(Text::_('COM_ADMINTOOLS') . ' &ndash; <small>' . $subtitle . '</small>', 'admintools');
	}

	protected function onBeforeBrowse()
	{
		parent::onBeforeBrowse();

		$hash = 'admintools' . $this->getName();

		// ...ordering
		$platform        = $this->container->platform;
		$input           = $this->input;
		$this->order     = $platform->getUserStateFromRequest($hash . 'filter_order', 'filter_order', $input, 'admintools_scanalert_id');
		$this->order_Dir = $platform->getUserStateFromRequest($hash . 'filter_order_Dir', 'filter_order_Dir', $input, 'DESC');

		// ...filter state
		$this->filters['status']       = $platform->getUserStateFromRequest($hash . 'filter_status', 'status', $input);
		$this->filters['path']         = $platform->getUserStateFromRequest($hash . 'filter_path', 'path', $input);
		$this->filters['acknowledged'] = $platform->getUserStateFromRequest($hash . 'filter_acknowledged', 'acknowledged', $input);

		// Construct the array of sorting fields
		$this->sortFields = [
			'admintools_scanalert_id' => 'ID',
			'path'                    => Text::_('COM_ADMINTOOLS_LBL_SCANALERTS_PATH'),
			'filestatus'              => Text::_('COM_ADMINTOOLS_LBL_SCANALERTS_STATUS'),
			'threat_score'            => Text::_('COM_ADMINTOOLS_LBL_SCANALERTS_THREAT_SCORE'),
			'acknowledged'            => Text::_('COM_ADMINTOOLS_LBL_SCANALERTS_ACKNOWLEDGED'),
		];

		if ($this->getLayout() == 'print')
		{
			$this->onBeforePrint();
		}
	}
}
