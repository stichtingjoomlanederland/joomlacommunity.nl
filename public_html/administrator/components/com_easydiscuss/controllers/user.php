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

class EasyDiscussControllerUser extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.users');
		$this->registerTask('add', 'edit');
		$this->registerTask('save', 'apply');
	}

	public function apply()
	{
		ED::checkToken();

		$userId = $this->input->get('id', 0, 'post', 'int');

		// Create a new JUser object
		$user = new JUser($userId);
		$original_gid = $user->get('gid');

		$post = $this->input->getArray('post');
		$user->name	= $post['fullname'];

		// this is so that we can get com_users jform data for custom fields.
		$profileExModel = new EDUsersModelRegistration();
		$form = $profileExModel->getForm();

		// here we need to get the custom fiels data and simulate the form post data.
		if ($form !== false) {
			$customFields = array();
			foreach ($form->getFieldsets() as $group => $fieldset) {

				if (strpos($group, 'fields') !== false) {
					$fields = $form->getFieldset($group);

					foreach($fields as $field) {
						$customFields[$field->fieldname] = $field->value;
					}
				}
			}

			if ($customFields) {
				$post['com_fields'] = $customFields;
			}
		}

		if (!$user->bind($post)) {
			ED::setMessage($user->getError(), ED_MSG_ERROR);
			$this->_saveError($user->id);
		}

		if( $user->get('id') == $this->my->get('id') && $user->get('block') == 1) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_CANNOT_BLOCK_YOURSELF'), ED_MSG_ERROR);
			$this->_saveError($user->id);

		} else if (($user->authorise('core.admin')) && $user->get('block') == 1) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_CANNOT_BLOCK_SUPERUSER'), ED_MSG_ERROR);
			$this->_saveError($user->id);

		} else if (($user->authorise('core.admin')) && !($this->my->authorise('core.admin'))) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_CANNOT_EDIT_SUPERUSER'), ED_MSG_ERROR);
			$this->_saveError($user->id);
		}

		//replacing thr group name with group id so it is save correctly into the Joomla group table.
		$jformPost = $this->input->get('jform', array(), 'post', 'array');

		if (!empty($jformPost['groups'])) {

			$user->groups = array();

			foreach ($jformPost['groups'] as $groupid) {
				$user->groups[$groupid] = $groupid;
			}
		}

		// Are we dealing with a new user which we need to create?
		$isNew = ($user->get('id') < 1);

		// Lets save the JUser object
		if (!$user->save()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_CANNOT_SAVE_THE_USER_INFORMATION'), ED_MSG_ERROR);
			return $this->execute('edit');
		}

		$post = $this->input->getArray('post');

		if ($isNew) {
			// if this is a new account, we unset the id so
			// that profile jtable will add new record properly.
			unset($post['id']);
		}

		$profile = ED::table('Profile');
		$profile->load($user->id);

		$file = $this->input->files->get('Filedata', '');
		$post['signature'] = $this->input->get('signature', '', 'raw');
		$post['description'] = $this->input->get('description', '', 'raw');

		if (!empty($file['name'])) {
			$profile->bindAvatar($file);
		}

		if (isset($post['address']) && !empty($post['address'])) {
			$post['location'] = $post['address'];
		}

		$profile->bind($post);

		if (isset($post['content'])) {
			$profile->signature = $post['content'];
		}

		//save params
		$userparams	= ED::getRegistry('');

		if (isset($post['facebook'])) {
			$userparams->set('facebook', $post['facebook']);
		}

		if (isset($post['show_facebook'])) {
			$userparams->set('show_facebook', $post['show_facebook']);
		}

		if (isset($post['twitter'])) {
			$userparams->set('twitter', $post['twitter']);
		}

		if (isset($post['show_twitter'])) {
			$userparams->set('show_twitter', $post['show_twitter']);
		}

		if (isset($post['linkedin'])) {
			$userparams->set('linkedin', $post['linkedin']);
		}

		if (isset($post['show_linkedin'])) {
			$userparams->set('show_linkedin', $post['show_linkedin']);
		}

		if (isset($post['skype'])) {
			$userparams->set('skype', $post['skype']);
		}

		if (isset($post['show_skype'])) {
			$userparams->set('show_skype', $post['show_skype']);
		}

		if ( isset($post['website'])) {
			$userparams->set('website', $post['website']);
		}

		if (isset($post['show_website'])) {
			$userparams->set('show_website', $post['show_website']);
		}

		$profile->params = $userparams->toString();

		// Save site details
		$siteDetails = ED::getRegistry('');

		if (isset($post['siteUrl'])) {
			$siteDetails->set( 'siteUrl', $post['siteUrl'] );
		}

		if (isset($post['siteUsername'])) {
			$siteDetails->set('siteUsername', $post['siteUsername']);
		}

		if (isset($post['sitePassword'])) {
			$siteDetails->set('sitePassword', $post['sitePassword']);
		}

		if (isset($post['ftpUrl'])) {
			$siteDetails->set('ftpUrl', $post['ftpUrl']);
		}

		if (isset($post['ftpUsername'])) {
			$siteDetails->set('ftpUsername', $post['ftpUsername']);
		}

		if (isset($post['ftpPassword'])) {
			$siteDetails->set('ftpPassword', $post['ftpPassword']);
		}

		if (isset($post['optional'])) {
			$siteDetails->set('optional', $post['optional']);
		}

		$profile->site	= $siteDetails->toString();
		$profile->store();

		// log the current action into database.
		$actionlog = ED::actionlog();
        $actionlogUserPermalink = $actionlog->normalizeActionLogUserPermalink($user->id);

		$actionlog->log('COM_ED_ACTIONLOGS_AUTHOR_UPDATED', 'user', array(
			'authorLink' => $actionlogUserPermalink,
			'authorName' => $user->username
		));

		// Update points
		ED::ranks()->assignRank($profile->id, 'points');

		$task = $this->getTask();
		$url = $task == 'apply' ? 'index.php?option=com_easydiscuss&view=users&layout=form&id=' . $profile->id : 'index.php?option=com_easydiscuss&view=users';

		ED::setMessage(JText::_('COM_EASYDISCUSS_USER_INFORMATION_SAVED'), 'success');
		ED::redirect($url);
	}

	function _saveError($id = '')
	{
		$url = $this->getTask() == 'apply' ? 'index.php?option=com_easydiscuss&view=users&layout=form&id=' . $profile->id : 'index.php?option=com_easydiscuss&view=users';

		ED::redirect($url);
	}

	function cancel()
	{
		return ED::redirect('index.php?option=com_easydiscuss&view=users');
	}

	public function remove()
	{
		// Check for request forgeries
		ED::checkToken();

		$cid = $this->input->get('cid', array(), 'array');
		EDArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			ED::setMessage('COM_EASYDISCUSS_SELECT_USER_TO_DELETE', ED_MSG_ERROR);

			return ED::redirect('index.php?option=com_easydiscuss&view=users');
		}

		$result = null;

		foreach ($cid as $id) {

			$result = null;

			if (ED::getJoomlaVersion() >= '1.6') {
				$result	= $this->_removeUser16($id);
			} else {
				$result	= $this->_removeUser($id);
			}

			if (!$result['success']) {
				ED::setMessage($result['msg'] , ED_MSG_ERROR);
				ED::redirect('index.php?option=com_easydiscuss&view=users');
			}
		}

		ED::setMessage($result['msg'], 'success');
		ED::redirect('index.php?option=com_easydiscuss&view=users');
	}

	private function logout()
	{
		// Check for request forgeries
		ED::checkToken();

		$task = $this->getTask();

		$cids = $this->input->get('cid', array(), '', 'array');
		$client = $this->input->get('client', 0, '', 'int');
		$id = $this->input->get('id', 0 , '', 'int');

		EDArrayHelper::toInteger($cids);

		if (count($cids) < 1) {
			ED::redirect('index.php?option=com_easydiscuss&view=users', JText::_('COM_EASYDISCUSS_USER_DELETED'));
			return false;
		}

		foreach($cids as $cid) {

			$options = array();

			if ($task == 'logout' || $task == 'block') {
				$options['clientid'][] = 0; //site
				$options['clientid'][] = 1; //administrator
			} else if ($task == 'flogout') {
				$options['clientid'][] = $client;
			}

			$this->app->logout((int)$cid, $options);
		}

		$msg = JText::_('COM_EASYDISCUSS_USER_SESSION_ENDED');

		switch ($task) {
			case 'flogout':
				ED::redirect('index.php', $msg);
				break;

			case 'remove':
			case 'block':
				return;
				break;

			default:
				ED::redirect('index.php?option=com_easydiscuss&view=users', $msg);
				break;
		}
	}

	private function _removeUser16($id)
	{
		$db	= ED::db();
		$currentUser = JFactory::getUser();
		$user = JFactory::getUser($id);
		$isUserSA= $user->authorise('core.admin');

		if ($isUserSA) {
			$msg = JText::_('You cannot delete a Super Administrator');

		} else if ($id == $currentUser->get('id')) {
			$msg = JText::_('You cannot delete Yourself!');

		} else {

			$count = 2;

			if ($isUserSA) {
				$saUsers = ED::getSAUsersIds();
				$count = count($saUsers);
			}

			if ($count <= 1 && $isUserSA) {
				// cannot delete Super Admin where it is the only one that exists
				$msg = "You cannot delete this Super Administrator as it is the only active Super Administrator for your site";

			} else {
				// delete user
				$user->delete();
				$msg = JText::_('User Deleted.');

				$this->input->set('task', 'remove');
				$this->input->set('cid', $id);

				// delete user acounts active sessions
				$this->logout();
				$success = true;
			}

		}

		$result['success'] = $success;
		$result['msg'] = $msg;

		return $result;
	}

	private function _removeUser($id)
	{
		$db	= ED::db();
		$currentUser = JFactory::getUser();
		$acl = JFactory::getACL();

		// check for a super admin ... can't delete them
		$objectID = $acl->get_object_id('users', $id, 'ARO');
		$groups	= $acl->get_object_groups( $objectID, 'ARO');
		$this_group	= strtolower($acl->get_group_name($groups[0], 'ARO'));

		$success = false;
		$msg = '';

		if ($this_group == 'super administrator') {
			$msg = JText::_('COM_EASYDISCUSS_CANNOT_EDIT_SUPER_ADMIN_ACCOUNT');

		} else if ($id == $currentUser->get('id')) {
			$msg = JText::_('COM_EASYDISCUSS_CANNOT_DELETE_SELF');

		} else if (($this_group == 'administrator') && ($currentUser->get('gid') == 24)) {
			$msg = JText::_('WARNDELETE');

		} else {
			$user = JUser::getInstance((int)$id);
			$count = 2;

			if ($user->get('gid') == 25) {
				// count number of active super admins
				$query = 'SELECT COUNT( id )'
					. ' FROM #__users'
					. ' WHERE gid = 25'
					. ' AND block = 0'
				;
				$db->setQuery($query);
				$count = $db->loadResult();
			}

			if ($count <= 1 && $user->get('gid') == 25) {
				// cannot delete Super Admin where it is the only one that exists
				$msg = "You cannot delete this Super Administrator as it is the only active Super Administrator for your site";

			} else {
				// delete user
				$user->delete();
				$msg = JText::_('COM_EASYDISCUSS_USER_DELETED');

				$this->input->set('task', 'remove');
				$this->input->set('cid', $id);

				// delete user acounts active sessions
				$this->logout();
				$success = true;
			}
		}

		$result['success'] = $success;
		$result['msg'] = $msg;

		return $result;
	}

	/**
	 * Remove user Avatar
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function removeAvatar()
	{
		// Check for request forgeries
		ED::checkToken();
		$id = $this->input->get('id', 0, 'int');
		$user = ED::User($id);

		$state = $user->deleteAvatar();

		ED::setMessage(JText::_('COM_EASYDISCUSS_USER_AVATAR_REMOVED'), 'success');

		return ED::redirect('index.php?option=com_easydiscuss&view=users&layout=form&id=' . $user->id);
	}

	/**
	 * Delete user download request
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function purgeAll()
	{
		// Check for request forgeries
		ED::checkToken();

		$model = ED::model('Download');
		$model->purgeRequests();

		$redirection = 'index.php?option=com_easydiscuss&view=users&layout=downloads';

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_AUTHOR_PURGED_GDPR_DOWNLOADS', 'user', array(
			'link' => $redirection
		));

		ED::setMessage('COM_ED_USER_DOWNLOAD_PURGE_ALL_SUCCESS', 'success');
		ED::redirect($redirection);
	}

	/**
	 * Delete user download request
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function removeRequest()
	{
		// Check for request forgeries
		ED::checkToken();

		$cid = $this->input->get('cid', array(), '', 'array');
		EDArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			throw ED::exception('COM_EASYDISCUSS_INVALID_ID', ED_MSG_ERROR);
		}

		// log the current action into database.
		$actionlog = ED::actionlog();

		$result = null;
		$redirection = 'index.php?option=com_easydiscuss&view=users&layout=downloads';

		foreach ($cid as $id) {

			$table = ED::table('Download');
			$table->load($id);

			// Retrieve the requester user id
			$author = ED::user($table->userid);

			// delete the request download data now
			$state = $table->delete();

			if ($state) {
				$actionlogUserPermalink = $actionlog->normalizeActionLogUserPermalink($author->id);

				$actionlog->log('COM_ED_ACTIONLOGS_AUTHOR_DELETED_GDPR_DOWNLOAD', 'user', array(
					'authorLink' => $actionlogUserPermalink,
					'authorName' => $author->getUsername()
				));	
			}
		}

		ED::setMessage('COM_ED_USER_DOWNLOAD_DELETE_SUCCESS', 'success');
		ED::redirect($redirection);
	}

	/**
	 * Assign badge for the user
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function assignBadge()
	{
		ED::checkToken();

		$badgeId = $this->input->get('badgeId', '', 'int');
		$userIds = $this->input->get('userIds', '', 'default');

		$redirectURL = 'index.php?option=com_easydiscuss&view=users';

		$userIds = base64_decode($userIds);
		$userIds = explode(',', $userIds);

		if (!$badgeId || !$userIds) {
			ED::setMessage('COM_EASYDISCUSS_INVALID_ID', ED_MSG_ERROR);
 			return $this->ajax->reject($redirectURL);
		}

		$badgeTbl = ED::table('badges');
		$badgeTbl->load($badgeId);

		if (!$badgeTbl->id) {
			ED::setMessage('COM_EASYDISCUSS_INVALID_ID', ED_MSG_ERROR);
 			return $this->ajax->reject($redirectURL);
		}

		$dateAchieved = ED::date()->toSql();
		$badgeUser = ED::table('BadgesUsers');
		$badges = ED::badges();

		// log the current action into database.
		$actionlog = ED::actionlog();

		foreach ($userIds as $userId) {

			$hasAchieved = $badgeUser->loadByUser($userId, $badgeId);

			// Skip this if this badge user already achieve
			if ($hasAchieved) {
				continue;
			}

			$badges->create($userId, $badgeId, $dateAchieved);

       		$author = ED::user($userId);
	        $actionlogUserPermalink = $actionlog->normalizeActionLogUserPermalink($userId);
			$actionlog->log('COM_ED_ACTIONLOGS_AUTHOR_ASSIGN_BADGE', 'user', array(
				'authorLink' => $actionlogUserPermalink,
				'authorName' => $author->getUsername(),
				'badgeTitle' => $badgeTbl->title,
				'badgeLink' => 'index.php?option=com_easydiscuss&view=badges&layout=form&id=' . $badgeId
			));
		}

		ED::setMessage('COM_ED_ASSIGN_BADGE_SUCCESS', 'success');

 		return $this->ajax->resolve($redirectURL);
	}	
}