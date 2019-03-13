<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussAjax extends EasyDiscuss
{
	public $commands = array();

	/**
	 * Processes ajax calls made on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function process()
	{
		// Get the namespace
		$namespace = $this->input->get('namespace', '', 'default');

		// Determines if this is an ajax call made to the site
		$isAjaxCall = $this->input->get('format', '', 'cmd') == 'ajax' && !empty($namespace);

		// If this is not an ajax call, there's no point proceeding with this.
		if(!$isAjaxCall) {
			return false;
		}

		// Process namespace string.
		// Legacy uses '.' as separator, we need to replace occurences of '.' with /
		$namespace = str_ireplace('.', '/', $namespace);
		$namespace = explode('/', $namespace);

		// @rule: All calls should be made a minimum out of 3 parts of dots (.)
		if (count($namespace) < 4) {
			$this->fail(JText::_('COM_EASYBLOG_INVALID_AJAX_CALL'));
			return $this->send();
		}

		/**
		 * Namespaces are broken into the following
		 *
		 * site/views/viewname/methodname - Front end ajax calls
		 * admin/views/viewname/methodname - Back end ajax calls
		 */
		list($location, $type, $name, $method) = $namespace;

		if ($type != 'views' && $type != 'controllers') {
			$this->fail(JText::_('Ajax calls are currently only serving views and controllers.'));
			return $this->send();
		}

		// Get the location
		$location = strtolower($location);
		$name = strtolower($name);

		$path = $location == 'admin' ? JPATH_ROOT . '/administrator' : JPATH_ROOT;
		$path .= '/components/com_easydiscuss';

		if ($type == 'views') {
			$path .= '/' . $type . '/' . $name . '/view.ajax.php';
		}

		if ($type == 'controllers') {
			$path .= '/' . $type . '/' . $name . '.php';
		}


		$classType = $type == 'views' ? 'View' : 'Controller';
		$class = 'EasyDiscuss' . $classType . preg_replace('/[^A-Z0-9_]/i', '', $name);

		if (!class_exists($class)) {

			jimport('joomla.filesystem.file');

			$exists = JFile::exists($path);

			if (!$exists) {
				$this->fail(JText::_('File does not exist.'));
				return $this->send();
			}

			require_once($path);
		}

		$obj = new $class();

		if (!method_exists($obj, $method)) {
			$this->fail(JText::sprintf('The method %1s does not exists.', $method));
			return $this->send();
		}

		// Call the method
		$obj->$method();

		return $this->send();
	}

	/**
	 * Allows caller to add commands to the ajax response chain
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function addCommand($type, &$data)
	{
		$this->commands[] = array('type' => $type, 'data' => &$data);

		return $this;
	}

	/* This will handle all ajax commands e.g. success/fail/script */
	public function __call($method, $args)
	{
		$this->addCommand($method, $args);

		return $this;
	}

	public function verifyAccess($allowGuest=false)
	{
		if (!JSession::checkToken('request')) {
			$this->reject(EB::exception('Invalid token'));
			$this->send();
		}

		if (!$allowGuest) {
			$my = JFactory::getUser();
			if ($my->guest) {
				$this->reject(EB::exception('You are not logged in!'));
				$this->send();
			}
		}
	}

	/**
	 * Responds to an ajax request
	 *
	 * @since	4.0
	 * @access	public	
	 */
	public function send()
	{
		// Isolate PHP errors and send it using notify command.
		$error_reporting = ob_get_contents();
		if (strlen(trim($error_reporting))) {
			$this->notify($error_reporting, 'debug');
		}
		ob_clean();

		// JSONP transport
		$callback = $this->input->get('callback', '', 'default');

		if ($callback) {
			header('Content-type: application/javascript; UTF-8');
			echo $callback . '(' . json_encode($this->commands) . ');';
			exit;
		}

		// IFRAME transport
		$transport = JRequest::getVar('transport');
		if ($transport=="iframe") {
			header('Content-type: text/html; UTF-8');
			echo '<textarea data-type="application/json" data-status="200" data-statusText="OK">' . json_encode($this->commands) . '</textarea>';
			exit;
		}

		// XHR transport
		header('Content-type: text/x-json; UTF-8');
		echo json_encode($this->commands);
		exit;
	}
}
