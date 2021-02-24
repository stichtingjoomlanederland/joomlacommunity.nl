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

class EasyDiscussViewDashboard extends EasyDiscussView
{
	/**
	 * Displays dashboard for admins and moderators
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		ED::setPageTitle(JText::_('COM_EASYDISCUSS_DASHBOARD_TITLE'));

		// Set the meta for the page
		ED::setMeta();

		$user =	ED::profile();

		if (!$user->canAccessDashboard()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_YOU_ARE_NOT_ALLOWED_HERE'), 'error');
			return ED::redirect(EDR::_('view=index', false));
		}

		$holidays = [];

		if ($this->config->get('main_work_schedule') && $this->acl->allowed('manage_holiday')) {
			$holidaysModel = ED::model('holidays');
			$holidays = $holidaysModel->getHolidays();
		}

		$posts = [];

		// Only retrieve pending post when site admin viewing the dashboard
		if (ED::isSiteAdmin() || $this->acl->allowed('manage_pending')) { 
			$model = ED::model("Threaded");
			$options = [
				'stateKey' => 'pending',
				'pending' => true
			];

			$result = $model->getPosts($options);
			$pagination = $model->getPagination();
			$posts = array();

			if ($result) {
				foreach ($result as $row) {
					$post = ED::post($row);

					if ($post->isQuestion()) {
						$post->editLink = 'index.php?option=com_easydiscuss&view=post&layout=pending&id=' . $post->id;
					}

					$posts[] = $post;
				}
			}
		}

		$this->set('posts', $posts);
		$this->set('holidays', $holidays);
		
		return parent::display('dashboard/default');
	}

	/**
	 * Displays create new holiday page
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function holidayForm($tmpl = null)
	{
		ED::setPageTitle(JText::_('COM_EASYDISCUSS_EDIT_HOLIDAYS_TITLE'));

		if (!$this->acl->allowed('manage_holiday')) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_YOU_ARE_NOT_ALLOWED_HERE'), 'error');
			return ED::redirect('index.php?option=com_easydiscuss');
		}

		$id = $this->input->get('id', '');

		if (!$id) {
			ED::setPageTitle(JText::_('COM_EASYDISCUSS_CREATE_HOLIDAYS_TITLE'));
		}

		// Load the holiday
		$holiday = ED::holiday($id);

		$this->set('holiday', $holiday);

		parent::display('dashboard/holidays/form');
	}	
}
