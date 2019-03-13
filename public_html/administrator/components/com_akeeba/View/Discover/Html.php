<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\View\Discover;

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Backup\Admin\Model\Discover;
use Akeeba\Engine\Factory;
use FOF30\View\DataView\Html as BaseView;
use JText;

class Html extends BaseView
{
	/**
	 * The directory we are currently listing
	 *
	 * @var  string
	 */
	public $directory;

	/**
	 * The list of importable archive files in the current directory
	 *
	 * @var  array
	 */
	public $files;

	public function onBeforeMain()
	{
		$this->addJavascriptFile('media://com_akeeba/js/Configuration.min.js');

		/** @var Discover $model */
		$model = $this->getModel();

		$this->directory = '';
		$directory = $model->getState('directory', '', 'path');

		if (empty($directory))
		{
			$config = Factory::getConfiguration();
			$this->directory = $config->get('akeeba.basic.output_directory', '[DEFAULT_OUTPUT]');
		}

		// Push translations
		JText::script('COM_AKEEBA_CONFIG_UI_BROWSE');
		JText::script('COM_AKEEBA_FILEFILTERS_LABEL_UIROOT');
	}

	public function onBeforeDiscover()
	{
		/** @var Discover $model */
		$model = $this->getModel();

		$directory = $model->getState('directory', '', 'path');
		$this->setLayout('discover');

		$files = $model->getFiles();

		$this->files = $files;
		$this->directory = $directory;
	}
}
