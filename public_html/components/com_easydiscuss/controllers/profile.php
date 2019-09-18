<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyDiscussControllerProfile extends EasyDiscussController
{
	/**
	 * To Display the view
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$viewName = JRequest::getCmd( 'view', $this->getName() );
		$view = $this->getView( $viewName,'',  $viewType);
		$view->display();
	}

	/**
	 * Store user profile data
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function saveProfile()
	{
		// Check for request forgeries
		ED::checkToken();

		$mainframe = $this->app;
		$config = ED::config();

		$post = $this->input->getArray('post');

		array_walk($post, array($this, '_trim'));

		if (!$this->_validateProfileFields($post)) {
			$this->setRedirect(EDR::_('view=profile&layout=edit', false));
			return;
		}

		// load user
		$my	= $this->my;
		$profile = ED::table('Profile');
		$profile->load($my->id);

		// this is so that we can get com_users jform data for custom fields.
		$profileExModel = new EasyDiscussModelProfileEx();
		$form = $profileExModel->getForm();
		$userRequiredBind = false;

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
				$userRequiredBind = true;
			}
		}

		$my->name = $post['fullname'];

		// We check for password2 instead off password because apparently it is still autofill the form although is autocomplete="off"
		if (!empty($post['password2'])) {
			$my->password = $post['password'];
			$userRequiredBind = true;
		}

		// we check for this because user have chance to modify their email as well
		if (!empty($post['email']) && $my->email != $post['email']) {
			$userRequiredBind = true;
		}

		if ($userRequiredBind) {
			$my->bind($post);
		}

		// Cheap fix: Do not allow user to override `edited` field.
		// Ideally, this should just be passed as ignore into the table.
		if (isset($post['edited'])) {
			unset($post['edited']);
		}

		$address = isset($post['address1']) && $post['address1'] ? $post['address1'] : '';

		if (isset($post['address']) && !empty($post['address'])) {
			$address = $post['address'];
		}

		// column mapping.
		$post['location'] = $address;

		if (isset($post['alias'])) {

			// perform alias checking here.
			$alias = $this->checkAlias($post['alias']);

			if (!$alias) {
				$this->setRedirect(EDR::_('view=profile&layout=edit'));
				return;
			}
		}

		$post['signature'] = $this->input->get('signature', '', 'raw');
		$post['description'] = $this->input->get('description', '', 'raw');

		$profile = ED::table('Profile');
		$profile->load($my->id);
		$profile->bind($post);

		//save avatar
		$file = JRequest::getVar( 'Filedata', '', 'files', 'array' );

		if (! empty($file['name'])) {
			$newAvatar = $this->_upload( $profile );

			// @rule: If this is the first time the user is changing their profile picture, give a different point
			if ($profile->avatar == 'default.png') {

				// @rule: Process AUP integrations
				ED::Aup()->assign(DISCUSS_POINTS_NEW_AVATAR, $my->id, $newAvatar);
			} else {
				// @rule: Process AUP integrations
				ED::Aup()->assign(DISCUSS_POINTS_UPDATE_AVATAR, $my->id, $newAvatar);
			}

			// @rule: Badges when they change their profile picture
			ED::history()->log('easydiscuss.new.avatar', $my->id, JText::_('COM_EASYDISCUSS_BADGES_HISTORY_UPDATED_AVATAR'));

			ED::badges()->assign('easydiscuss.new.avatar', $my->id);
			ED::points()->assign('easydiscuss.new.avatar', $my->id);

			// Reset the points
			$profile->updatePoints();

			$profile->avatar = $newAvatar;
		}

		// Save user params
		$userparams	= ED::registry();

		// Assign all the params in an array
		$userParamsArr = array(
						'facebook',
						'show_facebook',
						'twitter',
						'show_twitter',
						'linkedin',
						'show_linkedin',
						'skype',
						'show_skype',
						'website',
						'show_website');

		foreach ($userParamsArr as $key) {
			if (isset($post[$key])) {
				$userparams->set($key, $post[$key]);
			}
		}

		$profile->params = $userparams->toString();

		// Save site details
		$siteDetails = ED::registry();

		// Assign the site details into an array
		$siteDetailsArr = array(
						'siteUrl',
						'siteUsername',
						'sitePassword',
						'ftpUrl',
						'ftpUsername',
						'ftpPassword',
						'optional');

		foreach ($siteDetailsArr as $key) {
			if (isset($post[$key])) {
				$siteDetails->set($key, $post[$key]);
			}
		}

		$profile->site = $siteDetails->toString();

		if ($profile->store() && $my->save(true)) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_PROFILE_SAVED'), 'info');

			// @rule: Badges when they change their profile picture
			ED::history()->log('easydiscuss.update.profile', $my->id, JText::_('COM_EASYDISCUSS_BADGES_HISTORY_UPDATED_PROFILE'));
			ED::badges()->assign('easydiscuss.update.profile', $my->id);

			// Only give points the first time the user edits their profile.
			if (!$profile->edited) {
				ED::points()->assign('easydiscuss.update.profile', $my->id);

				$profile->edited = true;
				$profile->store();
			}
		} else {
			ED::setMessage(JText::_('COM_EASYDISCUSS_PROFILE_SAVE_ERROR'), 'error');
			$this->app->redirect(EDR::_('view=profile&layout=edit', false));
			return;
		}

		$this->app->redirect(EDR::_('view=profile&layout=edit', false));
		return;
	}

	public function _trim(&$text)
	{
		$text = JString::trim($text);
	}

	public function _validateProfileFields($post)
	{
		if (JString::strlen($post['fullname']) == 0) {
			$message = JText::_('COM_EASYDISCUSS_REALNAME_EMPTY');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);
			return false;
		}

		if (JString::strlen($post['nickname']) == 0) {
			$message = JText::_('COM_EASYDISCUSS_NICKNAME_EMPTY');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);
			return false;
		}

		if (JString::strlen($post['email']) == 0) {
			$message = JText::_('COM_ED_EMAIL_EMPTY');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);
			return false;
		}

		$validEmail = ED::string()->isValidEmail($post['email']);

		if (!$validEmail) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_EMAIL_ADDRESS');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);
			return false;
		}

		if (!empty($post['password'])) {

			if (JString::strlen($post['password']) < 4) {
				$message = JText::_('COM_EASYDISCUSS_PROFILE_PASSWORD_TOO_SHORT');
				ED::setMessage($message, DISCUSS_QUEUE_ERROR);
				return false;
			}

			// since this first password user did enter, we need to check for second password field as well
			if (empty($post['password2'])) {
				$message = JText::_('COM_EASYDISCUSS_PROFILE_PASSWORD_NOT_MATCH');
				ED::setMessage($message, DISCUSS_QUEUE_ERROR);
				return false;
			}
		}

		if (!empty($post['password2'])) {

			if ($post['password'] != $post['password2']) {
				$message = JText::_('COM_EASYDISCUSS_PROFILE_PASSWORD_NOT_MATCH');
				ED::setMessage($message, DISCUSS_QUEUE_ERROR);
				return false;
			}
		}

		return true;
	}

	public function _upload($profile, $type = 'profile')
	{
		$newAvatar  = '';

		//can do avatar upload for post in future.
		$newAvatar  = ED::uploadAvatar($profile);

		return $newAvatar;
	}

	public function removePicture()
	{
		$my = JFactory::getUser();

		if (!$my->id) {
			return $this->setRedirect(EDR::_('index.php?option=com_easydiscuss', false));
		}

		$profile = ED::user($my->id);

		// Delete the user's avatar.
		$profile->deleteAvatar();

		ED::setMessage(JText::_('COM_EASYDISCUSS_PROFILE_AVATAR_REMOVED_SUCCESSFULLY'), DISCUSS_QUEUE_SUCCESS);

		$url = EDR::_('view=profile&layout=edit', false);

		$this->setRedirect($url);
	}

	public function disableUser()
	{
		// Only allow site admin to disable this.
		if (!ED::isSiteAdmin()) {
			return $this->setRedirect( EDR::_( 'index.php?option=com_easydiscuss' , false ) );
		}

		$userId = $this->input->get('id', 0, 'int');
		$db = ED::db();
		$query = 'UPDATE ' . $db->nameQuote( '#__users' )
				. ' SET ' . $db->nameQuote( 'block' ) . '=' . $db->quote( 1 )
				. ' WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->quote( $userId );

		$db->setQuery( $query );
		$result = $db->query();

		if (!$result) {
			return $this->setRedirect(EDR::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $userId , false));
		}

		$message = JText::_('COM_EASYDISCUSS_USER_DISABLED');
		ED::setMessageQueue($message , DISCUSS_QUEUE_SUCCESS);
		$this->setRedirect(EDR::_( 'index.php?option=com_easydiscuss' , false ));
	}

	/**
	 * Checks if an alias is valid
	 * @since	4.0
	 * @access	public
	 */
	public function checkAlias($alias)
	{
		$db = ED::db();
		$query	= 'SELECT `alias` FROM `#__discuss_users` WHERE `alias` = ' . $db->quote($alias) . ' '
				. 'AND ' . $db->nameQuote('id') . '!=' . $db->Quote($this->my->id);
		$db->setQuery( $query );

		$exists = $db->loadResult();

		if ($exists != NULL) {
			$message = JText::_('COM_EASYDISCUSS_ALIAS_NOT_AVAILABLE');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);
			return false;
		}

		return true;
	}

	/**
	 * Submit request to download information
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function download()
	{
		// Check for request forgeries
		ED::checkToken();

		// Ensure that the user is logged in
		if (!$this->my->id) {
			return JError::raiseError(500, JText::_('COM_EASYDISCUSS_PLEASE_LOGIN_INFO'));
		}

		$table = ED::table('download');
		$exists = $table->load(array('userid' => $this->my->id));

		if ($exists) {
			return JError::raiseError(500, JText::_('COM_ED_GDPR_DOWNLOAD_ERROR_MULTIPLE_REQUEST'));
		}

		$params = array();

		$table->userid = $this->my->id;
		$table->state = DISCUSS_DOWNLOAD_REQ_NEW;
		$table->params = json_encode($params);
		$table->created = ED::date()->toSql();
		$table->store();

		$redirect = EDR::_('view=profile&layout=download', false);

		$message = JText::_('COM_ED_GDPR_REQUEST_DATA_SUCCESS');
		ED::setMessageQueue($message , DISCUSS_QUEUE_SUCCESS);

		return $this->app->redirect($redirect);
	}
}


require_once(JPATH_SITE.'/components/com_users/models/profile.php');
class EasyDiscussModelProfileEx extends UsersModelProfile
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
		JForm::addFormPath(JPATH_SITE . '/components/com_users/models/forms');
		JForm::addFieldPath(JPATH_SITE . '/components/com_users//models/fields');
		JForm::addFormPath(JPATH_SITE . '/components/com_users//model/form');
		JForm::addFieldPath(JPATH_SITE . '/components/com_users//model/field');

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
