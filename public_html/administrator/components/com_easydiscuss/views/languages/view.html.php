<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewLanguages extends EasyDiscussAdminView
{
	/**
	 * Renders the category listing at the back end
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function display($tpl = null)
	{
		// Set page attributes
		$this->title('COM_EASYDISCUSS_LANGUAGES_TITLE');

		JToolbarHelper::custom('discover' , 'refresh' , '' , JText::_('COM_EASYDISCUSS_TOOLBAR_BUTTON_DISCOVER') , false);
		JToolbarHelper::custom('purge' , 'purge' , '' , JText::_('COM_EASYDISCUSS_TOOLBAR_BUTTON_PURGE_CACHE'), false);
		JToolbarHelper::custom('install', 'upload' , '' , JText::_('COM_ED_TOOLBAR_BUTTON_INSTALL'));
		JToolbarHelper::custom('uninstall', 'remove' , '' , JText::_('COM_ED_TOOLBAR_BUTTON_UNINSTALL'));

		// Get the languages that are already stored on the db
		$model = ED::model('Languages');
		$initialized = $model->initialized();

		// Get the api key from the config
		$key = $this->config->get('main_apikey');

		$this->set('key', $key);

		if (!$initialized) {
			return parent::display('languages/initialize');
		}

		// Get languages
		$languages 	= $model->getLanguages();

		foreach ($languages as &$language) {

			$translators = json_decode($language->translator);

			$language->translator = $translators;
		}

		$this->set('languages', $languages);

		parent::display('languages/default');
	}
}
