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

class EasyDiscussControllerEmails extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.emails');

		$this->registerTask('apply', 'save');
	}

	/**
	 * Saves an email template
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function save()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the contents of the email template
		$contents = $this->input->get('source', '', 'raw');
		
		$file = $this->input->get('file', '', 'default');
		$file = base64_decode($file);

		// Get the overriden path
		$model = ED::model("Emails");
		$path = JPATH_ROOT . '/templates/' . $model->getCurrentTemplate() . '/html/com_easydiscuss/emails/' . $file;

		JFile::write($path, $contents);

		ED::setMessage('COM_EASYDISCUSS_EMAILS_TEMPLATE_FILE_SAVED_SUCCESSFULLY');
		
		$this->app->redirect('index.php?option=com_easydiscuss&view=emails');
	}
}
