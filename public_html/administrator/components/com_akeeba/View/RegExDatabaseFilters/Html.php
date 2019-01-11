<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\View\RegExDatabaseFilters;

// Protect from unauthorized access
use Akeeba\Backup\Admin\Model\DatabaseFilters;
use Akeeba\Backup\Admin\Model\RegExDatabaseFilters;
use Akeeba\Backup\Admin\View\ViewTraits\ProfileIdAndName;
use Akeeba\Engine\Platform;
use JHtml;
use JText;

defined('_JEXEC') or die();

class Html extends \FOF30\View\DataView\Html
{
	use ProfileIdAndName;

	/**
	 * SELECT element for choosing a database root
	 *
	 * @var  string
	 */
	public $root_select = '';

	/**
	 * List of database roots
	 *
	 * @var  array
	 */
	public $roots = [];

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
		$this->addJavascriptFile('media://com_akeeba/js/RegExDatabaseFilters.min.js');

		/** @var RegExDatabaseFilters $model */
		$model = $this->getModel();

		/** @var DatabaseFilters $dbFilterModel */
		$dbFilterModel = $this->getModel('DatabaseFilters');

		// Get a JSON representation of the available roots
		$root_info = $dbFilterModel->get_roots();
		$roots     = array();
		$options   = array();

		if (!empty($root_info))
		{
			// Loop all dir definitions
			foreach ($root_info as $def)
			{
				$roots[] = $def->value;
				$options[] = JHtml::_('select.option', $def->value, $def->text);
			}
		}

		$site_root = '[SITEDB]';
		$attribs = 'onchange="akeeba.Regexdbfilters.activeRootChanged();"';

		$this->root_select = JHtml::_('select.genericlist', $options, 'root', $attribs, 'value', 'text', $site_root, 'active_root');
		$this->roots = $roots;

		// Get a JSON representation of the directory data
		$json = json_encode($model->get_regex_filters($site_root));
		$this->json = $json;

		$this->getProfileIdAndName();

		// Push translations
		JText::script('COM_AKEEBA_FILEFILTERS_LABEL_UIROOT');
		JText::script('COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER');
		JText::script('COM_AKEEBA_DBFILTER_TYPE_REGEXTABLES');
		JText::script('COM_AKEEBA_DBFILTER_TYPE_REGEXTABLEDATA');
	}
}
