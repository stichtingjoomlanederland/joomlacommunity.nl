<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
	public function insertBadge()
	{
		$userId = $this->input->get('userId', 0, 'int');
		$id = $this->input->get('id', 0, 'int');
		$ajax	= DiscussHelper::getHelper( 'Ajax' );

		// This shouldn't even be happening at all.
		if( !$id || !$userId ) {
			return $ajax->reject( JText::_( 'COM_EASYDISCUSS_INVALID_ID' ) );
		}

		$profile = ED::user($userId);

		if (!$profile->addBadge($id)) {
			return $ajax->reject( $profile->getError() );
		}

		$badge 	= DiscussHelper::getTable( 'Badges' );
		$badge->load( $id );

		$badgeUser 	= DiscussHelper::getTable( 'BadgesUsers' );
		$badgeUser->loadByUser( $id , $badge->id );

		$badge->reference_id 	= $badgeUser->id;
		$badge->custom 			= $badgeUser->custom;

		$user 		= JFactory::getUser( $userId );
		$this->set( 'badges' 	, array( $badge ) );
		$this->set( 'user'	, $user );
		$html = $this->loadTemplate( 'badge_item' );

		$ajax->resolve( $html );
	}

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
		
		if ($state) {
			return $this->ajax->resolve(true, JText::_('COM_EASYDISCUSS_USER_BADGE_CUSTOM_MESSAGE'));
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
