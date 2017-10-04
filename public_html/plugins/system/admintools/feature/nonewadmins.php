<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2017 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureNonewadmins extends AtsystemFeatureAbstract
{
	protected $loadOrder = 210;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		$fromBackend = $this->cparams->getValue('nonewadmins', 0) == 1;
		$fromFrontend = $this->cparams->getValue('nonewfrontendadmins', 1) == 1;

		$enabled = $fromBackend && $this->container->platform->isBackend();
		$enabled |= $fromFrontend && $this->container->platform->isFrontend();

		return $enabled;
	}

	/**
	 * Disables creating new admins or updating new ones
	 */
	public function onAfterInitialise()
	{
		$input  = $this->input;
		$option = $input->getCmd('option', '');
		$task   = $input->getCmd('task', '');
		$gid    = $input->getInt('gid', 0);

		if ($option != 'com_users' && $option != 'com_admin')
		{
			return;
		}

		$jform = $this->input->get('jform', array(), 'array');

		$allowedTasks = array('save', 'apply', 'user.apply', 'user.save', 'user.save2new', 'profile.apply', 'profile.save');

		if (!in_array($task, $allowedTasks))
		{
			return;
		}

		// Not editing, just core devs using the same task throughout the component, dammit
		if (empty($jform))
		{
			return;
		}

		$groups = array();

		if(isset($jform['groups']))
		{
			$groups = $jform['groups'];
		}

		$user = $this->container->platform->getUser((int)$jform['id']);

		// Sometimes $user->groups is null... let's be 100% sure that we loaded all the groups of the user
		if(empty($user->groups))
		{
			$user->groups = JUserHelper::getUserGroups($user->id);
		}

		if (!empty($user->groups))
		{
			foreach ($user->groups as $title => $gid)
			{
				if (!in_array($gid, $groups))
				{
					$groups[] = $gid;
				}
			}
		}

		$isAdmin = $this->hasAdminGroup($groups);

		if ($isAdmin)
		{
			// Get the correct reason (was the user being created in front- or back-end)?
			$reason = $this->container->platform->isBackend() ? 'nonewadmins' : 'nonewfrontendadmins';

			// Log and autoban security exception
			$extraInfo = "Submitted JForm Variables :\n";
			$extraInfo .= print_r($jform, true);
			$extraInfo .= "\n";
			$this->exceptionsHandler->logAndAutoban($reason, $extraInfo);

			// Throw an exception to prevent Joomla! processing this form
			$jlang = JFactory::getLanguage();
			$jlang->load('joomla', JPATH_ROOT, 'en-GB', true);
			$jlang->load('joomla', JPATH_ROOT, $jlang->getDefault(), true);
			$jlang->load('joomla', JPATH_ROOT, null, true);

			throw new Exception(JText::_('JGLOBAL_AUTH_ACCESS_DENIED'), '403');
		}
	}

	/**
	 * Hooks into the Joomla! models before a user is saved. This catches the case where a 3PD extension tries to create
	 * a new user instead of going through com_users.
	 *
	 * @param   JUser|array     $oldUser  The existing user record
	 * @param   bool            $isNew    Is this a new user?
	 * @param   array           $data     The data to be saved
	 *
	 * @throws  Exception  When we catch a security exception
	 */
	public function onUserBeforeSave($oldUser, $isNew, $data)
	{
		$isAdmin = $this->hasAdminGroup($data['groups']);

		if ($isAdmin)
		{
			if ($oldUser instanceof JUser)
			{
				$oldUser = $oldUser->getProperties();
			}

			// If edited fields are in the whitelist, we should allow the edit
			if ($this->allowEdit($oldUser, $data))
			{
				return;
			}

			// Get the correct reason (was the user being created in front- or back-end)?
			$reason = $this->container->platform->isBackend() ? 'nonewadmins' : 'nonewfrontendadmins';

			// Log and autoban security exception
			$extraInfo = "User Data Variables :\n";
			$extraInfo .= print_r($data, true);
			$extraInfo .= "\n";
			$this->exceptionsHandler->logAndAutoban($reason, $extraInfo);

			// Throw an exception to prevent Joomla! processing this form
			$jlang = JFactory::getLanguage();
			$jlang->load('joomla', JPATH_ROOT, 'en-GB', true);
			$jlang->load('joomla', JPATH_ROOT, $jlang->getDefault(), true);
			$jlang->load('joomla', JPATH_ROOT, null, true);

			throw new Exception(JText::_('JGLOBAL_AUTH_ACCESS_DENIED'), '403');
		}
	}

	/**
	 * Changed fields are in the whitelist? If so I should allow the edit, even if we're dealing with a Super User
	 *
	 * @param   array   $oldUser    Old user details
	 * @param   array   $newUser    New user details
	 *
	 * @return  bool    Am I allowed to edit this user?
	 */
	private function allowEdit($oldUser, $newUser)
	{
		$fieldlist = array('id', 'name', 'username', 'email', 'password', 'block', 'sendEmail', 'registerDate',
							'lastvisitDate', 'activation', 'params', 'lastResetTime', 'resetCount', 'otpKey', 'otep', 'requireReset');

		$whitelist  = array('lastvisitDate', 'block', 'otpKey', 'otep', 'activation');

		// If all edited fields are inside the whitelist, we should allow the edit
		foreach ($newUser as $field => $new_value)
		{
			// Some fields are created on the fly by Joomla, so we can ignore any changes to them
			if (!in_array($field, $fieldlist))
			{
				continue;
			}

			// mhm... the new field is not inside the original user. This should never happen, but let's be safe
			// than sorry and block the request
			// DO NOT USE ISSET since some keys could be initialized to NULL
			if (!array_key_exists($field, $oldUser))
			{
				return false;
			}

			$old_value = $oldUser[$field];

			// New and old value are different, change detected!
			if ($old_value != $new_value)
			{
				// Am I really allowed to change this field?
				if (!in_array($field, $whitelist))
				{
					return false;
				}
			}
		}

		// If I'm here, it means that I can really edit this user (field in whitelist or no changes at all)
		return true;
	}

	/**
	 * Does any of the groups in the list have backend privileges
	 *
	 * @param   array  $groups
	 *
	 * @return  bool
	 */
	private function hasAdminGroup($groups)
	{
		$isAdmin = false;

		if (!empty($groups))
		{
			foreach ($groups as $group)
			{
				// First try to see if the group has explicit backend login privileges
				$backend = JAccess::checkGroup($group, 'core.login.admin', 1);

				// If not, is it a Super Admin (ergo inherited privileges)?
				if (is_null($backend))
				{
					$backend = JAccess::checkGroup($group, 'core.admin', 1);
				}

				$isAdmin |= $backend;
			}

			return $isAdmin;
		}

		return $isAdmin;
	}
}