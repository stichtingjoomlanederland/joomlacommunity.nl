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

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewDashboard extends EasyDiscussView
{
	/**
	 * Displays Holiday management page
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		ED::setPageTitle(JText::_('COM_EASYDISCUSS_HOLIDAYS_TITLE'));

		// Set the meta for the page
		ED::setMeta();

		if (!$this->acl->allowed('manage_holiday')) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_YOU_ARE_NOT_ALLOWED_HERE'), 'error');
			return $this->app->redirect('index.php?option=com_easydiscuss');
		}

		$model = ED::model('holidays');
		$holidays = $model->getHolidays();

		
		$this->set('holidays', $holidays);
		parent::display('dashboard/default');
	}

	/**
	 * Displays create new holiday page
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function form($tmpl = null)
	{
		ED::setPageTitle(JText::_('COM_EASYDISCUSS_EDIT_HOLIDAYS_TITLE'));

		if (!$this->acl->allowed('manage_holiday')) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_YOU_ARE_NOT_ALLOWED_HERE'), 'error');
			return $this->app->redirect('index.php?option=com_easydiscuss');
		}

		$id = $this->input->get('id', '');

		if (!$id) {
			ED::setPageTitle(JText::_('COM_EASYDISCUSS_CREATE_HOLIDAYS_TITLE'));
		}

		// Load the holiday
		$holiday = ED::holiday($id);

		$this->set('holiday', $holiday);

		parent::display('dashboard/form');
	}
}
