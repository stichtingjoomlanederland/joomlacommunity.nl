<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\View\IncludeFolders;

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Backup\Admin\Model\IncludeFolders;
use Akeeba\Backup\Admin\View\ViewTraits\ProfileIdAndName;
use Akeeba\Engine\Platform;
use FOF30\View\DataView\Html as BaseView;
use JText;

class Html extends BaseView
{
	use ProfileIdAndName;

	/**
	 * The view's interface data encoded in JSON format
	 *
	 * @var  string
	 */
	public $json = '';

	public function onBeforeMain()
	{
		$this->addJavascriptFile('media://com_akeeba/js/Configuration.min.js');
		$this->addJavascriptFile('media://com_akeeba/js/FileFilters.min.js');
		$this->addJavascriptFile('media://com_akeeba/js/IncludeFolders.min.js');

		// Get a JSON representation of the directories data
		/** @var IncludeFolders $model */
		$model = $this->getModel();
		$directories = $model->get_directories();
		$json = json_encode($directories);
		$this->json = $json;

		$this->getProfileIdAndName();

		// Push translations
		JText::script('COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER');
	}
}
