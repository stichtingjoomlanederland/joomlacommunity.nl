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

class EasydiscussControllerThemes extends EasyDiscussController
{
	/**
	 * Saves the custom.css contents
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function saveCustomCss()
	{
		$model = ED::model('Themes');
		$path = $model->getCustomCssTemplatePath();

		$contents = $this->input->get('contents', '', 'raw');

		JFile::write($path, $contents);

		ED::setMessage(JText::sprintf('COM_ED_THEMES_CUSTOM_CSS_SAVE_SUCCESS', $path), 'success');

		$redirect = 'index.php?option=com_easydiscuss&view=themes&layout=custom';

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_CUSTOMCSS_UPDATED', 'theme');

		return ED::redirect($redirect);
	}

	/**
	 * Allows caller to set a default theme
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function makeDefault()
	{ 
		$element = $this->input->get('cid', '', 'array');
		$element = $element[0];
		
		$redirect = 'index.php?option=com_easydiscuss&view=themes';

		if (!$element || !isset($element[0])) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_THEMES_INVALID_THEME'), ED_MSG_ERROR);
			return ED::redirect($redirect);
		}

		$data = array('layout_site_theme' => $element);

		$model = ED::model('Settings');
		$model->save($data);

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_THEME_DEFAULT', 'theme', array(
			'themeTitle' => $element
		));

		ED::setMessage(JText::_('COM_EASYDISCUSS_THEMES_SET_DEFAULT'), 'success');
		ED::redirect('index.php?option=com_easydiscuss&view=themes');
	}
}
