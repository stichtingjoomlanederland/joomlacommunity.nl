<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class EasydiscussControllerThemes extends EasyDiscussController
{
	public function getAjaxTemplate()
	{
		// Since this is the back end we need to load the front end's language file here.
		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

		$files = $this->input->get('names', '', 'var');

		if (empty($files)) {
			return false;
		}

		// Ensure the integrity of each items submitted to be an array.
		if (!is_array($files)) {
			$files = array($files);
		}

		$result	= array();

		foreach ($files as $file) {
			$theme = ED::themes();
			$contents = $theme->output($file . '.ejs');

			$obj = new stdClass();
			$obj->name = $file;
			$obj->content = $out;

			$result[] = $obj;
		}

		header('Content-type: text/javascript; UTF-8');
		echo json_encode($result);
		exit;
	}

	public function compile()
	{
		$less = ED::less();

		// Force compile
		$less->compileMode = 'force';

		$name = $this->input->get('name', null, 'GET');
		$type = $this->input->get('type', null, 'GET');

		$result = new stdClass();

		if (isset($name) && isset($type)) {

			switch ($type) {
				case "admin":
					$result = $less->compileAdminStylesheet($name);
					break;

				case "site":
					$result = $less->compileSiteStylesheet($name);
					break;

				case "module":
					$result = $less->compileModuleStylesheet($name);
					break;

				default:
					$result->failed = true;
					$result->message = "Stylesheet type is invalid.";
			}

		} else {
			$result->failed = true;
			$result->message = "Insufficient parameters provided.";
		}

		header('Content-type: text/javascript; UTF-8');
		echo json_encode($result);
		exit;
	}

	/**
	 * Allows caller to set a default theme
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function makeDefault()
	{ 
		$element = $this->input->get('cid', '', 'array');
		$element = $element[0];
		
		if (!$element || !isset($element[0])) {

			ED::setMessage(JText::_('COM_EASYDISCUSS_THEMES_INVALID_THEME'), 'error');

			return $this->app->redirect('index.php?option=com_easydiscuss&view=themes');
		}

		$data = array('layout_site_theme' => $element);

		$model = ED::model('Settings');
		$model->save($data);

		ED::setMessage(JText::_('COM_EASYDISCUSS_THEMES_SET_DEFAULT'), 'success');
		$this->app->redirect('index.php?option=com_easydiscuss&view=themes');
	}
}
