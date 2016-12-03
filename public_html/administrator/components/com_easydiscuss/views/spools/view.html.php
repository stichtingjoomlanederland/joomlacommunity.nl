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

class EasyDiscussViewSpools extends EasyDiscussAdminView
{
	/**
	 * Renders a list of email activities
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.spools');

		// Set the page attributes
		$this->title('COM_EASYDISCUSS_SPOOLS_TITLE');
		$this->desc('COM_EASYDISCUSS_SPOOLS_DESC');

		$model = ED::model('Spools', true);
		$mails = $model->getData();

		$pagination = $model->getPagination();

		// Filtering state
		$filter = $this->getUserState('spools.filter_state', 'filter_state', 'U', 'word');

		if ($mails) {
			foreach ($mails as &$mail) {
				$date = ED::date($mail->created);
				$mail->date = $date->toSql();
			}
		}

		$this->set('filter', $filter);
		$this->set('mails', $mails);
		$this->set('pagination', $pagination);

		parent::display('spools/default');
	}

	/**
	 * Previews a mail
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preview()
	{
		// Check for acl rules.
		$this->checkAccess('discuss.manage.spools');

		// Get the mail id
		$id = $this->input->get('id', 0, 'int');

		$mailq = ED::table('Mailqueue');
		$mailq->load($id);

		echo $mailq->getBody();
		exit;
	}

	/**
	 * Registers the toolbar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_SPOOLS_TITLE' ), 'spools' );

		JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();
		JToolBarHelper::divider();
		JToolBarHelper::custom('purge','purge','icon-32-unpublish.png', 'COM_EASYDISCUSS_SPOOLS_PURGE_ALL_BUTTON', false);
	}
}
