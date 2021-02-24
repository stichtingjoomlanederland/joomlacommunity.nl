<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussControllerHoliday extends EasyDiscussController
{
	/**
	 * This occurs when the user tries to create a new discussion or edits an existing discussion
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function save()
	{
		// Check for request forgeries
		ED::checkToken();

		// Determine if the user can truly edit the holiday
		$user =	ED::profile();

		if (!$user->canAccessDashboard()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_YOU_ARE_NOT_ALLOWED_HERE'), 'error');
			return ED::redirect(EDR::_('view=index', false));
		}
		
		// Get the id if available
		$id = $this->input->get('id', 0, 'int');

		// Load the holiday library
		$holiday = ED::holiday($id);

		$isNew = $holiday->id? false : true;

		$redirect = 'view=dashboard&layout=holidayForm';

		if (!$isNew) {
			$redirect .= '&id=' . $holiday->id;
		}

		// Get the date POST
		$data = $this->input->post->getArray();
		
		$holiday->bind($data);

		if (!$holiday->title) {
			ED::setMessage('COM_ED_HOLIDAY_ENTER_TITLE', 'error');
			return ED::redirect(EDR::_($redirect, false));
		}

		if (!$holiday->start || !$holiday->end) {
			ED::setMessage('COM_ED_HOLIDAY_ENTER_START_AND_END', 'error');
			return ED::redirect(EDR::_($redirect, false));
		}

		if ($holiday->end < $holiday->start) {
			ED::setMessage('COM_ED_HOLIDAY_END_DATE_LATER_THAN_START', 'error');
			return ED::redirect(EDR::_($redirect, false));
		}

		$holiday->save();

		$message = ($isNew)? 'COM_EASYDISCUSS_HOLIDAY_SAVED' : 'COM_EASYDISCUSS_EDIT_HOLIDAY_SUCCESS';
		
		ED::setMessage($message, 'success');
		ED::redirect(EDR::_('view=dashboard', false));
	}
}
