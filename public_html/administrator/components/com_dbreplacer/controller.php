<?php
/**
 * @package         DB Replacer
 * @version         6.4.0PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://regularlabs.com
 * @copyright       Copyright © 2021 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper as JComponentHelper;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\MVC\Controller\BaseController as JController;

/**
 * DB Replacer Default Controller
 */
class DBReplacerController extends JController
{
	/**
	 * Display Method
	 * Call the method and display the requested view
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$viewName   = JFactory::getApplication()->input->get('view', 'default');
		$viewLayout = JFactory::getApplication()->input->get('layout', 'default');

		if ($viewName == 'item')
		{
			// Hide the main menu
			JFactory::getApplication()->input->set('hidemainmenu', 1);
		}

		$view = $this->getView('default', JFactory::getDocument()->getType());

		// Get/Create the model
		$model = $this->getModel('default');
		if ($model)
		{
			// Push the model into the view ( as default )
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout($viewLayout);

		// Display the view
		$view->display();
	}

	/**
	 * Import Method
	 * Call the method and display the import view
	 */
	public function import()
	{
		JFactory::getApplication()->input->set('layout', 'import');
		$this->display();
	}

	/**
	 * Replace Method
	 * Set Redirection to the main administrator index
	 */
	public function replace()
	{
		$this->doReplace();
		$this->display();
	}

	/**
	 * Replace Method
	 * Set Redirection to the main administrator index
	 */
	private function doReplace()
	{
		$params          = (object) [];
		$params->table   = JFactory::getApplication()->input->get('table');
		$params->columns = JFactory::getApplication()->input->get('columns', [0], 'array');
		$params->search  = JFactory::getApplication()->input->get('search', '', 'RAW');

		if ( ! $params->table || $params->search == '' || ! is_array($params->columns) || empty($params->columns))
		{
			return;
		}

		// Get/Create the model
		if ( ! $model = $this->getModel(JFactory::getApplication()->input->get('view', 'default')))
		{
			return;
		}

		$params->replace = JFactory::getApplication()->input->get('replace', '', 'RAW');
		$params->case    = JFactory::getApplication()->input->getInt('case', 0);
		$params->where = JFactory::getApplication()->input->get('where', '', 'RAW');
		$params->regex = JFactory::getApplication()->input->getInt('regex', 0);
		$params->utf8  = JFactory::getApplication()->input->getInt('utf8', 0);
		$config        = JComponentHelper::getParams('com_dbreplacer');
		$params->max   = (int) $config->get('max_rows', '100');

		$model->replace($params);
	}
}
