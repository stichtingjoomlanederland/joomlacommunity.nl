<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
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
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
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
}
