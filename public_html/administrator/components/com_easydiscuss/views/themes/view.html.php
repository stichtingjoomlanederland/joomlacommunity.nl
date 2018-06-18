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

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewThemes extends EasyDiscussAdminView
{
	/**
	 * Renders the theme's listing
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.themes');

		// Set page attributes
		$this->title('COM_EASYDISCUSS_THEMES');

		// Register toolbar items
		JToolBarHelper::makeDefault('makeDefault');

		// Get all the themes
		$model = ED::model('themes');
		$themes = $model->getThemes();
	
		$this->set('default', $this->config->get('layout_site_theme'));
		$this->set('themes', $themes);

		parent::display('themes/default');
	}	

	/**
	 * Renders the custom css editor
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function custom()
	{
		$this->setHeading('COM_ED_THEMES_CUSTOM_CSS_HEADING', '', 'fa-edit');

		// Always use codemirror
		$editor = ED::getEditor('codemirror');

		$model = ED::model('Themes');
		$template = $model->getCurrentTemplate();

		JToolBarHelper::apply('saveCustomCss');

		// Get the custom.css override path for the current Joomla template
		$path = $model->getCustomCssTemplatePath();
		$contents = '';

		if (JFile::exists($path)) {
			$contents = JFile::read($path);
		}
		
		$this->set('contents', $contents);
		$this->set('editor', $editor);

		parent::display('themes/custom/default');
	}
}
