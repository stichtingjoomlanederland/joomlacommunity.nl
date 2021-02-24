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
	 * Resets a list of email template files to it's original state
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function reset()
	{
		$files = $this->input->get('cid', array(), 'default');

		if (!$files) {
			die('Invalid data provided');
		}

		$model = ED::model('Emails');

		foreach ($files as $file) {

			$file = base64_decode($file);
			
			$path = $model->getOverrideFolder($file);

			JFile::delete($path);
		}

		ED::setMessage('COM_ED_SELECTED_TEMPLATE_FILES_RESTORED_TO_ORIGINAL', 'success');
		ED::redirect('index.php?option=com_easydiscuss&view=emails');
	}

	/**
	 * Saves an email template
	 *
	 * @since	4.0
	 * @access	public	
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

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_MAILS_TEMPLATE_UPDATED', 'spools', array(
			'file' => str_ireplace('/', '', $file),
			'link' => 'index.php?option=com_easydiscuss&view=emails&layout=edit&file=' . urlencode($file)
		));

		ED::setMessage('COM_EASYDISCUSS_EMAILS_TEMPLATE_FILE_SAVED_SUCCESSFULLY', 'success');
		ED::redirect('index.php?option=com_easydiscuss&view=emails');
	}
}
