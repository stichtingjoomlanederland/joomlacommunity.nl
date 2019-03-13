<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\View\RemoteFiles;

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Backup\Admin\Model\RemoteFiles;
use FOF30\View\DataView\Html as BaseView;

class Html extends BaseView
{
	/**
	 * The available remote file actions
	 *
	 * @var  array
	 */
	public $actions;

	/**
	 * Total size of the file(s) to download
	 *
	 * @var  int
	 */
	public $total;

	/**
	 * Total size of downloaded file(s) so far
	 *
	 * @var  int
	 */
	public $done;

	/**
	 * Percentage of the total download complete, rounded to the nearest whole number (0-100)
	 *
	 * @var  int
	 */
	public $percent;

	/**
	 * The backup record ID we are downloading back to the server
	 *
	 * @var  int
	 */
	public $id;

	/**
	 * The part number currently being downloaded
	 *
	 * @var  int
	 */
	public $part;

	/**
	 * The fragment of the part currently being downloaded
	 *
	 * @var  int
	 */
	public $frag;

	/**
	 * Runs on the "listactions" task: lists all
	 */
	public function onBeforeListactions()
	{
		/** @var RemoteFiles $model */
		$model         = $this->getModel();
		$actions       = $model->getActions();
		$this->actions = $actions;

		$css = <<< CSS
dt.message { display: none; }
dd.message { list-style: none; }

CSS;

		$this->addCssInline($css);
	}

	public function onBeforeDltoserver()
	{
		/** @var RemoteFiles $model */
		$model = $this->getModel();

		$this->setLayout('dlprogress');

		// Get progress bar stats
		$total   = $this->container->platform->getSessionVar('dl_totalsize', 0, 'akeeba');
		$done    = $this->container->platform->getSessionVar('dl_donesize', 0, 'akeeba');

		$percent = 0;

		if ($total > 0)
		{
			$percent = (int)(100 * ($done / $total));
			$percent = max(0, $percent);
			$percent = min(100, $percent);
		}

		$this->total   = $total;
		$this->done    = $done;
		$this->percent = $percent;

		$this->id   = $model->getState('id', 0, 'int');
		$this->part = $model->getState('part', 0, 'int');
		$this->frag = $model->getState('frag', 0, 'int');

		// Render the progress bar
		$script = <<<JS


;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
akeeba.System.documentReady(function(){
	document.forms.adminForm.submit();
});

JS;
		$this->addJavascriptInline($script);

		$css = <<< CSS
dl { display: none; }

CSS;

		$this->addCssInline($css);

	}
}
