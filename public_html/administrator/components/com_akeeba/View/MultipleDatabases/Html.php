<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\View\MultipleDatabases;

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Backup\Admin\Model\MultipleDatabases;
use Akeeba\Backup\Admin\View\ViewTraits\ProfileIdAndName;
use Akeeba\Engine\Platform;
use FOF30\View\DataView\Html as BaseView;
use JText;

/**
 * View for database table exclusion
 */
class Html extends BaseView
{
	use ProfileIdAndName;

	/**
	 * The view's interface data encoded in JSON format
	 *
	 * @var  string
	 */
	public $json = '';

	/**
	 * Main page
	 */
	public function onBeforeMain()
	{
		// Load Javascript files
		$this->addJavascriptFile('media://com_akeeba/js/FileFilters.min.js');
		$this->addJavascriptFile('media://com_akeeba/js/MultipleDatabases.min.js');

		/** @var MultipleDatabases $model */
		$model = $this->getModel();

		// Get a JSON representation of the database connection data
		$databases  = $model->get_databases();
		$json       = json_encode($databases);
		$this->json = $json;

		$this->getProfileIdAndName();

		// Push translations
		JText::script('COM_AKEEBA_MULTIDB_GUI_LBL_LOADING');
		JText::script('COM_AKEEBA_MULTIDB_GUI_LBL_CONNECTOK');
		JText::script('COM_AKEEBA_MULTIDB_GUI_LBL_CONNECTFAIL');
		JText::script('COM_AKEEBA_MULTIDB_GUI_LBL_SAVEFAIL');
		JText::script('COM_AKEEBA_MULTIDB_GUI_LBL_LOADING');
	}
}
