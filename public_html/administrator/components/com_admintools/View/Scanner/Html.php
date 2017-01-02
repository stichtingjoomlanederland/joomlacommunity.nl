<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\Scanner;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Model\Scanner;
use FOF30\View\DataView\Html as BaseView;

class Html extends BaseView
{
	/**
	 * The file extensions which will be scanned
	 *
	 * @var  array
	 */
	public $fileExtensions;

	/**
	 * Folders to exclude from scanning
	 *
	 * @var  array
	 */
	public $excludeFolders;

	/**
	 * Files to exclude from scanning
	 *
	 * @var  array
	 */
	public $excludeFiles;

	/**
	 * Minimum execution time in seconds
	 *
	 * @var  int
	 */
	public $minExecTime;

	/**
	 * Maximum execution time in seconds
	 *
	 * @var  int
	 */
	public $maxExecTime;

	/**
	 * Execution time bias in percentage points
	 *
	 * @var  int
	 */
	public $runtimeBias;

	protected function onBeforeMain()
	{
		/** @var Scanner $model */
		$model = $this->getModel();

		$this->fileExtensions = $model->getFileExtensions();
		$this->excludeFolders = $model->getExcludeFolders();
		$this->excludeFiles   = $model->getExcludeFiles();
		$this->minExecTime    = $model->getMinExecTime();
		$this->maxExecTime    = $model->getMaxExecTime();
		$this->runtimeBias    = $model->getRuntimeBias();
	}
}