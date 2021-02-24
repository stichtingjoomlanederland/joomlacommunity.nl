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

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewUser extends EasyDiscussAdminView
{
	/**
	 * Add user's badge custom message
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function customMessage()
	{
		$badgeId = $this->input->get('badgeId');
		$customMessage = $this->input->get('customMessage','error', 'raw');
		$userId = $this->input->get('userId');

		if (!$badgeId) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_INVALID_ID'));
		}

		// Load the user's badge
		$badge = ED::table('BadgesUsers');
		$badge->loadByUser($userId, $badgeId);

		$badge->custom = $customMessage;
		$state = $badge->store();
		$message = ($badge->custom == '') ? JText::_('COM_EASYDISCUSS_USER_BADGE_REVERT_CUSTOM_MESSAGE') : JText::_('COM_EASYDISCUSS_USER_BADGE_CUSTOM_MESSAGE');
		
		if ($state) {
			return $this->ajax->resolve(true, $message);
		}

		return $this->ajax->reject(false, 'error');
	}

	/**
	 * Delete user's badge
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function deleteBadge()
	{
		$userId = $this->input->get('userId');
		$badgeId = $this->input->get('badgeId');

		// Checks the userId or badgeId is not provided.
		if (!$userId || !$badgeId) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_INVALID_ID'));
		}

		$badge = ED::table('BadgesUsers');
		$badge->loadByUser($userId, $badgeId);

		$state = $badge->delete();

		return $this->ajax->resolve(true, JText::_('COM_EASYDISCUSS_USER_BADGE_REMOVED'));
	}

	/**
	 * Remove user's avatar
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function removeAvatar()
	{
		$userId	= $this->input->get('userid');
	
		// This shouldn't even be happening at all.
		if (!$userId) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_INVALID_ID'));
		}

		$user = ED::user($userId);

		$state = $user->deleteAvatar();
 
		return $this->ajax->resolve($user->getAvatar(), JText::_('COM_EASYDISCUSS_USER_AVATAR_REMOVED'));	
	}
}
