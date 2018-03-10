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

jimport('joomla.utilities.date');
jimport('joomla.filesystem.file');

class plgUserEasydiscussProfile extends JPlugin
{
	public function exists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

		if (JFile::exists($file)) {
			require_once($file);
			return true;
		}

		return false;
	}

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onContentPrepareData($context, $data)
	{
		if (!$this->exists()) {
			return;
		}

		// Check we are manipulating a valid form.
		if (!in_array($context, array('com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile'))) {
			return true;
		}

		if (is_object($data)) {

			$userId = isset($data->id) ? $data->id : 0;

			if (!isset($data->easydiscussprofile) and $userId > 0) {

				// Load the profile data from the database.
				$db = JFactory::getDbo();
				$db->setQuery(
					'SELECT * FROM `#__discuss_users`' .
					' WHERE id = '.(int) $userId
				);

				$result = $db->loadObject();

				// Check for a database error.
				if ($db->getErrorNum()) {
					$this->_subject->setError($db->getErrorMsg());
					return false;
				}

				// Merge the profile data.
				$data->easydiscussprofile = array();
				
				if (isset($result->nickname) && $result->nickname) {
					$data->easydiscussprofile['nickname'] = $result->nickname;
				}

				if (isset($result->description) && $result->description) {
					$data->easydiscussprofile['description'] = $result->description;
				}

				if (isset($result->signature) && $result->signature) {
					$data->easydiscussprofile['signature'] = $result->signature;
				}

				if (isset($result->url) && $result->url) {
					$data->easydiscussprofile['website'] = $result->url;
				}

				if (isset($result->location) && $result->location) {
					$data->easydiscussprofile['location'] = $result->location;
				}

				if (!empty($result->params)) {
					
					$userparams	= ED::getRegistry($result->params);

					$data->easydiscussprofile['facebook'] = $userparams->get('facebook', '');
					$data->easydiscussprofile['twitter'] = $userparams->get('twitter', '');
					$data->easydiscussprofile['linkedin'] = $userparams->get('linkedin', '');
					$data->easydiscussprofile['skype'] = $userparams->get('skype', '');					
				}

				// Todo: clean data
				// ...
			}
		}

		return true;
	}

	public function onContentPrepareForm($form, $data)
	{
		if (!$this->exists()) {
			return;
		}

		if (!($form instanceof JForm)) {
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}

		// Check we are manipulating a valid form.
		$name = $form->getName();

		if (!in_array($name, array('com_admin.profile', 'com_users.user', 'com_users.profile', 'com_users.registration'))) {
			return true;
		}

		// Add the registration fields to the form.
		JForm::addFormPath(dirname(__FILE__) . '/profiles');
		$form->loadFile('profile', false);

		$fields = array(
			'nickname',
			'description',
			'signature',
			'facebook',
			'twitter',
			'linkedin',
			'skype',
			'website',
			'location'
		);

		foreach ($fields as $field) {

			// Case using the users manager in admin
			if ($name == 'com_users.user') {
				
				// Remove the field if it is disabled in registration and profile
				if ($this->params->get('register-require_' . $field, 1) == 0
					&& $this->params->get('profile-require_' . $field, 1) == 0) {

					$form->removeField($field, 'easydiscussprofile');
				}

			// Case registration	
			} elseif ($name == 'com_users.registration') {
				
				// Toggle whether the field is required.
				if ($this->params->get('register-require_' . $field, 1) > 0) {
					$form->setFieldAttribute($field, 'required', ($this->params->get('register-require_' . $field) == 2) ? 'required' : '', 'profile');
				} else {
					$form->removeField($field, 'easydiscussprofile');
				}

			// Case profile in site or admin	
			} elseif ($name == 'com_users.profile' || $name == 'com_admin.profile') {
				
				// Toggle whether the field is required.
				if ($this->params->get('profile-require_' . $field, 1) > 0) {
					$form->setFieldAttribute($field, 'required', ($this->params->get('profile-require_' . $field) == 2) ? 'required' : '', 'profile');
				} else {
					$form->removeField($field, 'easydiscussprofile');
				}
			}
		}

		return true;
	}

	public function onUserAfterSave($data, $isNew, $result, $error)
	{
		if (!$this->exists()) {
			return;
		}

		$userId	= JArrayHelper::getValue($data, 'id', 0, 'int');

		if ($userId && $result && isset($data['easydiscussprofile']) && (count($data['easydiscussprofile']))) {
			
			try {

				// Sanitize the date
				// ...

				// Save into profile table
				$profile = ED::user($userId);

				$profile->nickname = $data['easydiscussprofile']['nickname'];;
				$profile->description = $data['easydiscussprofile']['description'];
				$profile->signature	= $data['easydiscussprofile']['signature'];
				$profile->url = $data['easydiscussprofile']['website'];
				$profile->location = $data['easydiscussprofile']['location'];

				// Save params
				$userparams	= ED::getRegistry('');

				if (isset($data['easydiscussprofile']['facebook'])) {
					$userparams->set('facebook', $data['easydiscussprofile']['facebook']);
				}

				if (isset($data['easydiscussprofile']['twitter'])) {
					$userparams->set( 'twitter', $data['easydiscussprofile']['twitter']);
				}

				if (isset($data['easydiscussprofile']['linkedin'])) {
					$userparams->set( 'linkedin', $data['easydiscussprofile']['linkedin']);
				}

				if (isset($data['easydiscussprofile']['skype'])) {
					$userparams->set('skype', $data['easydiscussprofile']['skype']);
				}

				if ( isset($data['easydiscussprofile']['website'])) {
					$userparams->set('website', $data['easydiscussprofile']['website']);
				}

				$profile->params = $userparams->toString();

				if (!$profile->id) {
					$profile->id = $userId;
				}

				if (!$profile->store()) {
					throw new Exception($profile->getError());
				}

			} catch (JException $e) {
				$this->_subject->setError($e->getMessage());
				return false;
			}
		}

		return true;
	}

	public function onUserAfterDelete($user, $success, $msg)
	{
		if (!$this->exists()) {
			return;
		}
		// Easydiscuss User Plugin will take care of this

		return true;
	}
}