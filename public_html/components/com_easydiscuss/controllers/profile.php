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
		$viewName = $this->input->get('view', $this->getName(), 'cmd');
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

		// Handle twofactor posted data
		$twoFactorData = array();

		if (isset($post['jform'])) {
			$twoFactorData = $post['jform'];

			unset($post['jform']);
		}

		array_walk($post, array($this, 'trim'));

		// Validate the post items.
		if (!$this->validateProfile($post)) {
			$this->setRedirect(EDR::_('view=profile&layout=edit', false));
			return;
		}

		// Retrieveing user's custom fields from com_user jfrom data.
		$profileExModel = new EasyDiscussModelProfileEx();
		$form = $profileExModel->getForm();

		// Get the custom fields data and simulate the form post data.
		if ($form !== false) {
			$customFields = array();

			foreach ($form->getFieldsets() as $group => $fieldset) {
				if (strpos($group, 'fields') !== false) {
					$fields = $form->getFieldset($group);

					foreach ($fields as $field) {
						$customFields[$field->fieldname] = $field->value;
					}
				}
			}

			if ($customFields) {
				$post['com_fields'] = $customFields;
			}
		}

		// Set the name field.
		$this->my->name = $post['fullname'];

		// We check for password2 instead off password because apparently it is still autofill the form although is autocomplete="off"
		if (!empty($post['password2'])) {
			$this->my->password = $post['password'];
		}

		// we check for this because user have chance to modify their email as well
		if (!empty($post['email']) && $this->my->email != $post['email']) {
			$this->my->email = $post['email'];
		}

		$address = isset($post['address1']) && $post['address1'] ? $post['address1'] : '';

		if (isset($post['address']) && !empty($post['address'])) {
			$address = $post['address'];
		}

		// column mapping.
		$post['location'] = $address;

		$post['signature'] = $this->input->get('signature', '', 'raw');
		$post['description'] = $this->input->get('description', '', 'raw');

		$userSaved = $this->my->save(true);

		// Handle twofactor setup
		if (array_key_exists('twofactor', $twoFactorData)) {

			$joomlaUserModel = ED::getJoomlaUserModel();

			$twoFactorMethod = $twoFactorData['twofactor']['method'];

			$otpConfig = ED::getOtpConfig();

			// if the user first time setting this up
			if ($twoFactorMethod !== 'none' && $otpConfig->method == 'none') {
				$otpConfigReplies = ED::getTwoFactorConfig($twoFactorMethod);

				// Look for a valid reply
				foreach ($otpConfigReplies as $reply) {
					if (!is_object($reply) || empty($reply->method) || ($reply->method != $twoFactorMethod)) {
						continue;
					}

					$otpConfig->method = $reply->method;
					$otpConfig->config = $reply->config;

					break;
				}

				// Save OTP configuration.
				$state = $joomlaUserModel->setOtpConfig($this->my->id, $otpConfig);

				// Generate one time emergency passwords if required (depleted or not set)
				if (empty($otpConfig->otep)) {
					$joomlaUserModel->generateOteps($this->my->id);
				}
			} else {

				if ($twoFactorMethod == 'none') {
					// Default otpConfig
					$otpConfig->method = 'none';
					$otpConfig->config = array();
				}

				$joomlaUserModel->setOtpConfig($this->my->id, $otpConfig);
			}
		}

		$profile = ED::table('Profile');
		$profile->load($this->my->id);
		$profile->bind($post);

		//save avatar
		$file = $this->input->files->get('Filedata', '');

		if (!empty($file['name'])) {
			$acl = ED::acl();
			$profile->bindAvatar($file, $acl);
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
				'show_website'
		);

		foreach ($userParamsArr as $key) {
			if (isset($post[$key])) {
				$userparams->set($key, $post[$key]);
			}
		}

		$profile->params = $userparams->toString();

		// Render additional tabs
		JPluginHelper::importPlugin('easydiscuss');
		JFactory::getApplication()->triggerEvent('onBeforeSaveUser', array(&$profile, &$post));

		if ($profile->store() && $userSaved) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_PROFILE_SAVED'), 'success');

			// @rule: Badges when they change their profile picture
			ED::history()->log('easydiscuss.update.profile', $this->my->id, JText::_('COM_EASYDISCUSS_BADGES_HISTORY_UPDATED_PROFILE'));
			ED::badges()->assign('easydiscuss.update.profile', $this->my->id);

			// Only give points the first time the user edits their profile.
			if (!$profile->edited) {
				ED::points()->assign('easydiscuss.update.profile', $this->my->id);

				$profile->edited = true;
				$profile->store();
			}
		} else {
			ED::setMessage(JText::_('COM_EASYDISCUSS_PROFILE_SAVE_ERROR'), 'error');
			ED::redirect(EDR::_('view=profile&layout=edit', false));
			return;
		}

		ED::redirect(EDR::_('view=profile&layout=edit', false));
		return;
	}

	protected function trim(&$text)
	{
		$text = EDJString::trim($text);
	}

	protected function validateProfile($post)
	{
		if (EDJString::strlen($post['fullname']) == 0) {
			$message = JText::_('COM_EASYDISCUSS_REALNAME_EMPTY');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);
			return false;
		}

		if (EDJString::strlen($post['nickname']) == 0) {
			$message = JText::_('COM_EASYDISCUSS_NICKNAME_EMPTY');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);
			return false;
		}

		if (EDJString::strlen($post['email']) == 0) {
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

			if (EDJString::strlen($post['password']) < 4) {
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

		if (isset($post['alias']) && $post['alias']) {

			$validAlias = $this->checkAlias($post['alias']);

			if (!$validAlias) {
				$message = JText::_('COM_EASYDISCUSS_ALIAS_NOT_AVAILABLE');
				ED::setMessage($message, DISCUSS_QUEUE_ERROR);
				return false;
			}
		}

		return true;
	}

	/**
	 * Allows caller to remove their picture
	 *
	 * @since	5.0.0
	 * @access	public
	 */
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
		ED::setMessage($message , DISCUSS_QUEUE_SUCCESS);
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
			throw ED::exception('COM_EASYDISCUSS_PLEASE_LOGIN_INFO', ED_MSG_ERROR);
		}

		$table = ED::table('download');
		$exists = $table->load(array('userid' => $this->my->id));

		if ($exists) {
			throw ED::exception('COM_ED_GDPR_DOWNLOAD_ERROR_MULTIPLE_REQUEST', ED_MSG_ERROR);
		}

		$params = array();

		$table->userid = $this->my->id;
		$table->state = DISCUSS_DOWNLOAD_REQ_NEW;
		$table->params = json_encode($params);
		$table->created = ED::date()->toSql();
		$table->store();

		$redirect = EDR::_('view=profile&layout=download', false);

		$message = JText::_('COM_ED_GDPR_REQUEST_DATA_SUCCESS');
		ED::setMessage($message , DISCUSS_QUEUE_SUCCESS);

		return ED::redirect($redirect);
	}
}

class EasyDiscussModelProfileEx extends EDUsersModelProfile
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
