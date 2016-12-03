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
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewEmails extends EasyDiscussAdminView
{
	/**
	 * Renders a list of email template files
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.emails');

		// Set the page attributes
		$this->title('COM_EASYDISCUSS_EMAILS_TITLE');

		$model = ED::model('Emails');
		$mails = $model->getTemplates();

		$this->set('mails', $mails);

		parent::display('emails/default');
	}

	/**
	 * Renders the editor for email template
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function edit()
	{
		$this->checkAccess('discuss.manage.emails');

		// Set the page attributes
		$this->title('COM_EASYDISCUSS_EMAILS_EDITING_TITLE');

		JToolBarHelper::title(JText::_('COM_EASYDISCUSS_EMAILS_EDITING_TITLE'), 'emails');

		JToolBarHelper::apply();
		JToolBarHelper::cancel();

		$file = $this->input->get('file', '', 'default');
		$file = urldecode($file);

		$absolutePath = DISCUSS_ROOT . '/themes/wireframe/emails/' . $file;

		$model = ED::model("Emails");
		$file = $model->getTemplate($absolutePath, true);

		// Get the current editor
		$editor = JFactory::getEditor('codemirror');

		$this->set('editor', $editor);
		$this->set('file', $file);

		parent::display('emails/editor');		
	}
}
