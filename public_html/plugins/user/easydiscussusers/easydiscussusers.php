<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgUserEasyDiscussUsers extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
     * Performs existance of Easydiscuss
     *
     * @since   4.0
     * @access  public
     */
	public function exists()
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/easydiscuss.xml';
		$engine = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

		if (!JFile::exists($engine) || !JFile::exists($path)) {
		    return false;
		}

		require_once($engine);		

		jimport('joomla.filesystem.file');

		return true;
	}	

	/**
     * onUserAfterSave
     *
     * @since   4.0
     * @access  public
     */
	public function onUserAfterSave($data, $isNew, $result)
	{
		if ($result) {
			if ($this->exists()) {
				// Get backend subscribe model file
				$model = ED::model('Subscribe', true);
				$model->updateSubscriberEmail($data, $isNew);
			}
		}
	}

	/**
     * onUserBeforeDelete
     *
     * @since   4.0
     * @access  public
     */
	public function onUserBeforeDelete($user)
	{
		$this->onBeforeDeleteUser($user);
	}

	/**
     * onBeforeDeleteUser
     *
     * @since   4.0
     * @access  public
     */
	public function onBeforeDeleteUser($user)
	{
		if ($this->exists()) {
			$mainframe = JFactory::getApplication();

			$userId = $user['id'];
			$newOwnerShip = $this->_getnewOwnerShip($userId);

			//transfer ownership
			$this->ownerTransferTags($userId, $newOwnerShip);
			$this->ownerTransferPosts($userId, $newOwnerShip);
			$this->ownerTransferThread($userId, $newOwnerShip);
			$this->onwerTransferComments($userId, $newOwnerShip);

			//remove user and his related daata that cannot be transferred.
			$this->removeBadges($userId);
			$this->removeConversations($userId);
			$this->removeModerator($userId);
			$this->removeLikes($userId);
			$this->removeSubscription($userId);
			$this->removeVotes($userId);
			$this->removeEasyDiscussUser($userId);
		}
	}

	/**
     * Retrieve new ownership id
     *
     * @since   4.0
     * @access  public
     */
	public function _getnewOwnerShip($curUserId)
	{
		$econfig = ED::config();

		// this should get from backend. If backend not defined, get the default superadmin.
		$defaultSAid = ED::getDefaultSAIds();
		$newOwnerShipId	= $econfig->get('main_orphanitem_ownership', $defaultSAid);

		/**
		 * we check if the tobe deleted user is the same user id as the saved user id in config.
		 * 		 if yes, we try to get a next SA id.
		 */

		if ($curUserId == $newOwnerShipId) {
			
			$newOwnerShip = $this->_getSuperAdminId($curUserId);

			// this is no no a big no! try to get the next admin.
			if (ED::getJoomlaVersion() >= '1.6') {
				
				$saUsersId = ED::getSAUsersIds();
				
				if (count($saUsersId) > 0) {
					
					for ($i = 0; $i < count($saUsersId); $i++) {
						
						if ($saUsersId[$i] != $curUserId) {
							$newOwnerShip = $saUsersId[$i];
							break;
						}
					}
				}
			}
		}

		$newOwnerShipId	= $this->_verifyOnwerShip($newOwnerShipId);

		$db = ED::db();

		$query	= 'SELECT a.`id`, a.`name`, a.`username`, b.`nickname`, a.`email` '
				. ' FROM ' . $db->nameQuote('#__users') . ' as a '
				. ' INNER JOIN ' . $db->nameQuote('#__discuss_users') . ' as b on a.`id` = b.`id` '
				. ' WHERE a.`id` = ' . $db->Quote($newOwnerShipId);

		$db->setQuery($query);
		$result = $db->loadAssoc();

		$displayFormat = $econfig->get('layout_nameformat', 'name');
		$displayName = '';

		if ($displayFormat == 'name') {
			$displayName = $result['name'];
		}

		if ($displayFormat == 'username') {
			$displayName = $result['username'];
		}

		if ($displayFormat == 'nickname') {
			$displayName = (empty($result['nickname'])) ? $result['name'] : $result['nickname'];
		}

		$newOwnerShip = new stdClass();
		$newOwnerShip->id = $result['id'];
		$newOwnerShip->name = $displayName;
		$newOwnerShip->email = $result['email'];

		return $newOwnerShip;
	}

	/**
     * Verify Ownership
     *
     * @since   4.0
     * @access  public
     */
	public function _verifyOnwerShip($id)
	{
		$db = ED::db();

		$query  = 'SELECT `id` FROM `#__users` WHERE `id` = ' . $db->Quote($id);
		$db->setQuery($query);
		$result = $db->loadResult();

		if (empty($result)) {
			if (ED::getJoomlaVersion() >= '1.6') {
				$saUsersId = ED::getSAUsersIds();
				$result = $saUsersId[0];
			} else {
				$result = $this->_getSuperAdminId();
			}
		}

		return $result;
	}

	public function _getSuperAdminId($curUserId = '')
	{
		$db = ED::db();

		$query = 'SELECT `id` FROM `#__users`';
		$query .= ' WHERE (LOWER( usertype ) = ' . $db->Quote('super administrator');
		$query .= ' OR `gid` = ' . $db->Quote('25') . ')';

		if (!empty($curUserId)) {
			$query .= ' AND `id` != ' . $db->Quote($curUserId);
		}

		$query .= ' ORDER BY `id` ASC';
		$query .= ' LIMIT 1';

		$db->setQuery($query);
		$result = $db->loadResult();

		$result = (empty($result)) ? '62' : $result;
		return $result;
	}

	/**
     * Transer tags to the new owner
     *
     * @since   4.0
     * @access  public
     */
	public function ownerTransferTags($userId, $newOwnerShip)
	{
		$db = ED::db();

		$query = 'UPDATE `#__discuss_tags`';
		$query .= ' SET `user_id` = ' . $db->Quote($newOwnerShip->id);
		$query .= ' WHERE `user_id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			JError::raiseError(500, $db->stderr());
		}
	}

	/**
     * Transfer Posts to the new owner
     *
     * @since   4.0
     * @access  public
     */
	public function ownerTransferPosts($userId, $newOwnerShip)
	{
		$db = ED::db();

		$query = 'UPDATE `#__discuss_posts`';
		$query .= ' SET `user_id` = ' . $db->Quote($newOwnerShip->id) . ' ';
		$query .= ' WHERE `user_id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			JError::raiseError(500, $db->stderr());
		}
	}

	/**
     * Transfer Posts to the new owner
     *
     * @since   4.1.13
     * @access  public
     */
	public function ownerTransferThread($userId, $newOwnerShip)
	{
		$db = ED::db();

		$query = 'UPDATE `#__discuss_thread`';
		$query .= ' SET `user_id` = ' . $db->Quote($newOwnerShip->id);
		$query .= ' WHERE `user_id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			JError::raiseError(500, $db->stderr());
		}

		$query = 'UPDATE `#__discuss_thread`';
		$query .= ' SET `last_user_id` = ' . $db->Quote($newOwnerShip->id);
		$query .= ' WHERE `last_user_id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			JError::raiseError(500, $db->stderr());
		}	
	}

	/**
     * Transfer comments to the new owner
     *
     * @since   4.0
     * @access  public
     */
	public function onwerTransferComments($userId, $newOwnerShip)
	{
		$db = ED::db();

		$query = 'UPDATE `#__discuss_comments`';
		$query .= ' SET `user_id` = ' . $db->Quote($newOwnerShip->id) . ', ';
		$query .= ' `name` = ' . $db->Quote($newOwnerShip->name) . ', ';
		$query .= ' `email` = ' . $db->Quote($newOwnerShip->email) . ' ';
		$query .= ' WHERE `user_id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			JError::raiseError(500, $db->stderr());
		}
	}

	/**
	 * Removes all conversation from the particular user.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function removeConversations($userId)
	{
		$db = ED::db();

		// First we need to get all the conversation parent.
		$query = array();

		$query[] = 'SELECT DISTINCT(' . $db->nameQuote('conversation_id') . ')';
		$query[] = 'FROM ' . $db->nameQuote('#__discuss_conversations_message');
		$query[] = 'WHERE ' . $db->nameQuote('created_by') . '=' . $db->Quote($userId);
		$query = implode(' ', $query);
		$db->setQuery($query);

		$rows = $db->loadResultArray();

		if (!$rows) {
			$query = array();

			// this user might be the recepient. lets check again.
			$query[] = 'SELECT DISTINCT(' . $db->nameQuote('conversation_id') . ')';
			$query[] = 'FROM ' . $db->nameQuote('#__discuss_conversations_participants');
			$query[] = 'WHERE ' . $db->nameQuote('user_id') . '=' . $db->Quote($userId);
			$query = implode(' ', $query);
			$db->setQuery($query);

			$rows = $db->loadResultArray();
		}

		if (!$rows) {

			// no conversation found for this user.
			return true;
		}

		foreach ($rows as $row) {
			// Delete main conversation
			$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_conversations') . ' WHERE ' . $db->nameQuote('id') . '=' . $db->Quote($row);
			$db->setQuery($query);
			$db->Query();

			// Delete messages
			$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_conversations_message') . ' WHERE ' . $db->nameQuote('conversation_id') . '=' . $db->Quote($row);
			$db->setQuery($query);
			$db->Query();

			// Delete message maps
			$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_conversations_message_maps') . ' WHERE ' . $db->nameQuote('conversation_id') . '=' . $db->Quote($row);
			$db->setQuery($query);
			$db->Query();

			// Delete message participants
			$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_conversations_participants') . ' WHERE ' . $db->nameQuote('conversation_id') . '=' . $db->Quote($row);
			$db->setQuery($query);
			$db->Query();
		}

		return true;
	}

	/**
     * Remove all like records for this user
     *
     * @since   4.0
     * @access  public
     */
	public function removeLikes($userId)
	{
		$db = ED::db();

		$query = 'SELECT `content_id`, `type` FROM `#__discuss_likes`';
		$query .= ' WHERE `created_by` = ' . $db->Quote($userId) . ' ';
		$query .= ' AND `type` = ' . $db->Quote('post') . ' ';
		$db->setQuery($query);
		$likes = $db->loadObjectList();

		if (!empty($likes)) {
			
			foreach ($likes as $like) {
				
				$query = 'UPDATE `#__discuss_posts` ';
				$query .= ' SET `num_likes` = `num_likes` - 1 ';
				$query .= ' WHERE `id` = ' . $db->Quote($like->content_id);
				$db->setQuery($query);
				$db->query();
			}
		}

		$query = 'DELETE FROM `#__discuss_likes`';
		$query .= ' WHERE `created_by` = ' . $db->Quote($userId);
		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
	}

	/**
     * Remove all subscriptions for that particular user
     *
     * @since   4.0
     * @access  public
     */
	public function removeSubscription($userId)
	{
		$db = ED::db();

		$query = 'DELETE FROM `#__discuss_subscription`';
		$query .= ' WHERE `userid` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
	}

	/**
     * Remove all Votes for this user
     *
     * @since   4.0
     * @access  public
     */
	public function removeVotes($userId)
	{
		$db = ED::db();

		$query = 'SELECT `post_id`, `value` FROM `#__discuss_votes`';
		$query .= ' WHERE `user_id` = ' . $db->Quote($userId) . ' ';
		$db->setQuery($query);
		$votes = $db->loadObjectList();

		if (!empty($votes)) {
			foreach ($votes as $vote) {
				$query = 'UPDATE `#__discuss_posts` ';

					if ($vote->value == '-1') {

						$query .= ' SET `sum_totalvote` = `sum_totalvote` + 1';
						$query .= ' ,`num_negvote` = `num_negvote` - 1';
					} else {

						$query .= ' SET `sum_totalvote` = `sum_totalvote` - 1';
					}

				$query .= ' WHERE `id` = ' . $db->Quote($vote->post_id);
				$db->setQuery($query);
				$db->query();
				break;
			}
		}

		$query = 'DELETE FROM `#__discuss_votes`';
		$query .= ' WHERE `user_id` = ' . $db->Quote($userId);
		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
	}

	/**
     * Remove user from Easydiscuss table
     *
     * @since   4.0
     * @access  public
     */
	public function removeEasyDiscussUser($userId)
	{
		$db = ED::db();

		// Delete the user's avatar
		$query = 'SELECT `avatar` FROM `#__discuss_users`';
		$query .= ' WHERE `id` = ' . $db->Quote($userId) . ' ';
		$db->setQuery($query);
		$user = $db->loadAssoc();

		if (!empty($user) && $user['avatar']!='default.png') {

			$avatar_link = ED::image()->getAvatarRelativePath() . '/' . $user['avatar'];
			$imagePath = str_replace('/', DIRECTORY_SEPARATOR, $avatar_link);

			if (JFile::exists($imagePath)) {
				JFile::delete( $imagePath );
			}
		}

		$query = 'DELETE FROM `#__discuss_users`';
		$query .= ' WHERE `id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
	}

	/**
     * Remove badges for this user
     *
     * @since   4.0
     * @access  public
     */
	public function removeBadges($userId)
	{
		$model = ED::model('Badges');
		$state = $model->removeBadge($userId);

		if ($state == false) {
			JError::raiseError( 500, $db->stderr());
		}
	}

	/**
     * Remove category moderator
     *
     * @since   4.0
     * @access  public
     */
	public function removeModerator($userId)
	{
		$db = ED::db();

		$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_category_acl_map')
				. ' WHERE ' . $db->nameQuote('content_id') . '=' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();
	}
}
