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
		// Check for request forgeries
		ED::checkToken();

		$db	= ED::db();

		$userId = $this->input->get('id', 0, 'post', 'int');

		// Create a new JUser object
		$user = new JUser($userId);
		$original_gid = $user->get('gid');

		$post = $this->input->getArray('post');
		$user->name	= $post['fullname'];

		// this is so that we can get com_users jform data for custom fields.
		$profileExModel = new EasyDiscussModelProfileEx();
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
			ED::setMessage($user->getError(), 'error');
			$this->_saveError($user->id);
		}

		if( $user->get('id') == $this->my->get('id') && $user->get('block') == 1) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_CANNOT_BLOCK_YOURSELF'), 'error');
			$this->_saveError($user->id);

		} else if (($user->authorise('core.admin')) && $user->get('block') == 1) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_CANNOT_BLOCK_SUPERUSER'), 'error');
			$this->_saveError($user->id);

		} else if (($user->authorise('core.admin')) && !($this->my->authorise('core.admin'))) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_CANNOT_EDIT_SUPERUSER'), 'error');
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

		/*
		 * Lets save the JUser object
		 */
		if (!$user->save()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_CANNOT_SAVE_THE_USER_INFORMATION'), 'error');
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

		$file = JRequest::getVar('Filedata', '', 'files', 'array');
		$post['signature'] = $this->input->get('signature', '', 'raw');
		$post['description'] = $this->input->get('description', '', 'raw');

		if (!empty($file['name'])) {
			$newAvatar = ED::uploadAvatar($profile, true);
			$profile->avatar = $newAvatar;
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

		// Update points
		ED::ranks()->assignRank($profile->id, 'points');

		$task = $this->getTask();
		$url = $task == 'apply' ? 'index.php?option=com_easydiscuss&view=users&layout=form&id=' . $profile->id : 'index.php?option=com_easydiscuss&view=users';

		ED::setMessage(JText::_('COM_EASYDISCUSS_USER_INFORMATION_SAVED'), 'success');

		$this->app->redirect($url);
	}

	function _saveError($id = '')
	{
		$url = $this->getTask() == 'apply' ? 'index.php?option=com_easydiscuss&view=users&layout=form&id=' . $profile->id : 'index.php?option=com_easydiscuss&view=users';

		$this->app->redirect($url);
	}

	function cancel()
	{
		return $this->app->redirect('index.php?option=com_easydiscuss&view=users');
	}

	public function remove()
	{
		// Check for request forgeries
		ED::checkToken();

		$cid = $this->input->get('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_EASYDISCUSS_SELECT_USER_TO_DELETE', true));
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
				ED::setMessage($result['msg'] , 'error');
				$this->app->redirect( 'index.php?option=com_easydiscuss&view=users', $result['msg']);
			}
		}

		ED::setMessage($result['msg'], 'success');
		$this->app->redirect('index.php?option=com_easydiscuss&view=users', $result['msg']);
	}

	private function logout()
	{
		// Check for request forgeries
		ED::checkToken();

		$task = $this->getTask();

		$cids = $this->input->get('cid', array(), '', 'array');
		$client = $this->input->get('client', 0, '', 'int');
		$id = $this->input->get('id', 0 , '', 'int');

		JArrayHelper::toInteger($cids);

		if (count($cids) < 1) {
			$this->app->redirect('index.php?option=com_easydiscuss&view=users', JText::_('COM_EASYDISCUSS_USER_DELETED'));
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
				$this->app->redirect('index.php', $msg);
				break;

			case 'remove':
			case 'block':
				return;
				break;

			default:
				$this->app->redirect('index.php?option=com_easydiscuss&view=users', $msg);
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

		return $this->app->redirect('index.php?option=com_easydiscuss&view=users&layout=form&id=' . $user->id);
	}

	/**
	 * Delete user download request
	 * @since	4.1
	 * @access	public
	 */
	public function purgeAll()
	{
		// Check for request forgeries
		ED::checkToken();

		$model = ED::model('Download');
		$model->purgeRequests();

		ED::setMessage('COM_ED_USER_DOWNLOAD_PURGE_ALL_SUCCESS', 'success');
		$this->app->redirect('index.php?option=com_easydiscuss&view=users&layout=downloads');
	}

	/**
	 * Delete user download request
	 * @since	4.1
	 * @access	public
	 */
	public function removeRequest()
	{
		// Check for request forgeries
		ED::checkToken();

		$cid = $this->input->get('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_EASYDISCUSS_INVALID_ID', true));
		}

		$result = null;

		foreach ($cid as $id) {

			$table = ED::table('Download');
			$table->load($id);

			$table->delete();
		}

		ED::setMessage('COM_ED_USER_DOWNLOAD_DELETE_SUCCESS', 'success');
		$this->app->redirect('index.php?option=com_easydiscuss&view=users&layout=downloads');
	}
}


require_once(JPATH_ADMINISTRATOR .'/components/com_users/models/user.php');
class EasyDiscussModelProfileEx extends UsersModelUser
{
	/**
	 * @since	4.0
	 * @access	public
	 */
	protected $data;

	/**
	 * Checks if an alias is valid
	 * @since	4.0
	 * @access	public
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Get user custom fields if available
	 * @since	4.0
	 * @access	public
	 */
	public function getForm($data = array(), $loadData = true)
	{
		if (! $this->isEnabled()) {
			return false;
		}

		// add com_users forms and fields path
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_users/models/forms');
		JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_users//models/fields');
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_users//model/form');
		JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_users//model/field');

		$form = parent::getForm($data, $loadData);
		return $form;
	}

	/**
	 * Checks if custom fields supported or not.
	 * @since	4.0
	 * @access	public
	 */
	public function isEnabled()
	{
		JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

		// Only joomla 3.7.x and above have custom fields
		if (!class_exists('FieldsHelper')) {
			return false;
		}

		return true;
	}
}
