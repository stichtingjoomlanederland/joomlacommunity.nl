<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.application.component.controller');

class EasyDiscussController extends JControllerLegacy
{
	public function __construct()
	{
		parent::__construct();

		$this->doc = JFactory::getDocument();
		$this->app = JFactory::getApplication();
		$this->input = ED::request();
		$this->config = ED::config();
		$this->my = JFactory::getUser();
		$this->jconfig = ED::jconfig();
		$this->ajax = ED::ajax();
		$this->isAjax = $this->doc->getType() == 'ajax';

        if (method_exists($this, 'isFeatureAvailable')) {
        	$available = $this->isFeatureAvailable();

        	if (!$available) {
        		throw ED::exception('COM_EASYDISCUSS_FEATURE_IS_NOT_ENABLED', ED_MSG_ERROR);
        	}
        }
	}

	/**
	 * Override parent controller's display behavior.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function display($params = array(), $urlparams = false)
	{
		$type = $this->doc->getType();
		$name = $this->input->get('view', 'index', 'cmd');
		$view = $this->getView($name, $type, '');

		// Once we have the view, set the appropriate layout.
		$layout = $this->input->get('layout', 'default', 'cmd');

		$view->setLayout($layout);

		if ($layout == 'default') {
			$view->display();
			return;
		}

		if (!method_exists($view, $layout)) {
			$view->display();
			return;
		}

		call_user_func_array(array($view, $layout), $params);
		return;
	}
}
