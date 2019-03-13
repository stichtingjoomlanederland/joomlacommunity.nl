<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\View\S3Import;

use Akeeba\Backup\Admin\Model\S3Import;
use FOF30\View\DataView\Html as BaseView;

// Protect from unauthorized access
defined('_JEXEC') or die();

class Html extends BaseView
{
	public $s3access;
	public $s3secret;
	public $buckets;
	public $bucketSelect;
	public $contents;
	public $root;
	public $crumbs;
	public $total;
	public $done;
	public $percent;
	public $total_parts;
	public $current_part;

	public function onBeforeMain()
	{
		/** @var S3Import $model */
		$model = $this->getModel();
		$model->getS3Credentials();
		$contents     = $model->getContents();
		$buckets      = $model->getBuckets();
		$bucketSelect = $model->getBucketsDropdown();
		$platform     = $this->container->platform;
		$input        = $this->input;
		$root         = $platform->getUserStateFromRequest('com_akeeba.folder', 'folder', $input, '', 'raw');

		// Assign variables
		$this->s3access     = $model->getState('s3access');
		$this->s3secret     = $model->getState('s3secret');
		$this->buckets      = $buckets;
		$this->bucketSelect = $bucketSelect;
		$this->contents     = $contents;
		$this->root         = $root;
		$this->crumbs       = $model->getCrumbs();
	}

	public function onBeforeDltoserver()
	{
		/** @var S3Import $model */
		$this->setLayout('downloading');
		$model = $this->getModel();

		$total   = $this->container->platform->getSessionVar('s3import.totalsize', 0, 'com_akeeba');
		$done    = $this->container->platform->getSessionVar('s3import.donesize', 0, 'com_akeeba');
		$part    = $this->container->platform->getSessionVar('s3import.part', 0, 'com_akeeba') + 1;
		$parts   = $this->container->platform->getSessionVar('s3import.totalparts', 0, 'com_akeeba');

		$percent = 0;

		if ($total > 0)
		{
			$percent = (int)(100 * ($done / $total));
			$percent = max(0, $percent);
			$percent = min($percent, 100);
		}

		$this->total        = $total;
		$this->done         = $done;
		$this->percent      = $percent;
		$this->total_parts  = $parts;
		$this->current_part = $part;

		// Render the progress bar
		$step     = $model->getState('step', 1, 'int') + 1;

		$script = <<<JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
akeeba.System.documentReady(function(){
	window.location='index.php?option=com_akeeba&view=S3Import&layout=downloading&task=dltoserver&step=$step';
});

JS;
		$this->addJavascriptInline($script);
	}
}
