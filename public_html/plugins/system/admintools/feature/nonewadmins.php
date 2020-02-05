<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\User\User;

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

		$enabled  = $fromBackend && $this->container->platform->isBackend();
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

		if ($option != 'com_users' && $option != 'com_admin')
		{
			return;
		}

		$jform = $this->input->get('jform', array(), 'array');

		$filteredTasks = array(
			'save',
			'apply',
			'user.apply',
			'user.save',
			'user.save2new',
			'profile.apply',
			'profile.save',
		);

		if (!in_array($task, $filteredTasks))
		{
			return;
		}

		// Not editing, just core devs using the same task throughout the component, dammit
		if (empty($jform))
		{
			return;
		}

		$groups = array();

		if (isset($jform['groups']))
		{
			$groups = $jform['groups'];
		}

		$user = $this->container->platform->getUser((int) $jform['id']);

		// Sometimes $user->groups is null... let's be 100% sure that we loaded all the groups of the user
		if (empty($user->groups))
		{
			$user->groups = JUserHelper::getUserGroups($user->id);
		}

		$makingNewAdmin = $this->hasAdminGroup($groups);
		$isAdmin        = $this->hasAdminGroup($user->groups);
		$isFrontend     = $this->container->platform->isFrontend();

		if (!$isAdmin && !$makingNewAdmin)
		{
			return;
		}

		// Allow editing my own user record if I am already an admin
		if ($isAdmin && ($this->container->platform->getUser()->id == $jform['id']))
		{
			return;
		}

		/**
		 * In the frontend we only stop requests which are trying to make a new admin. This lets execution fall through
		 * to onUserBeforeSave where we can check for Joomla! 3.9+ user consent changes.
		 */
		$newUser = array_merge($jform);

		if (array_key_exists('params', $newUser))
		{
			$newUser['params'] = json_encode($newUser['params']);
		}

		if ($isFrontend && !$makingNewAdmin && $this->allowEdit($user))
		{
			return;
		}

		// Get the correct reason (was the user being created in front- or back-end)?
		$reason = $this->container->platform->isBackend() ? 'nonewadmins' : 'nonewfrontendadmins';

		// Log and autoban security exception
		$extraInfo = "Submitted JForm Variables :\n";
		$extraInfo .= print_r($jform, true);
		$extraInfo .= "\n";

		// Display the error only if the user should be really blocked (ie we're not in the Whitelist)
		if ($this->exceptionsHandler->logAndAutoban($reason, $extraInfo))
		{
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
		// Only applies to admin users.
		$isAdmin = $this->hasAdminGroup($data['groups']);

		if (!$isAdmin)
		{
			return;
		}

		$user = $this->container->platform->getUser($data['id']);

		// We are allowed to edit the profile of a user without active consent
		if (!$isNew && $this->allowEdit($user))
		{
			return;
		}

		// We can always edit out own profile
		if ($this->container->platform->getUser()->id == $data['id'])
		{
			return;
		}

		// Get the correct reason (was the user being created in front- or back-end)?
		$reason = $this->container->platform->isBackend() ? 'nonewadmins' : 'nonewfrontendadmins';

		// Log and autoban security exception
		$extraInfo = "User Data Variables :\n";
		$extraInfo .= print_r($data, true);
		$extraInfo .= "\n";

		// Display the error only if the user should be really blocked (ie we're not in the Whitelist)
		if ($this->exceptionsHandler->logAndAutoban($reason, $extraInfo))
		{
			// Throw an exception to prevent Joomla! processing this form
			$jlang = JFactory::getLanguage();
			$jlang->load('joomla', JPATH_ROOT, 'en-GB', true);
			$jlang->load('joomla', JPATH_ROOT, $jlang->getDefault(), true);
			$jlang->load('joomla', JPATH_ROOT, null, true);

			throw new Exception(JText::_('JGLOBAL_AUTH_ACCESS_DENIED'), '403');
		}
	}

	/**
	 * Is this a user who has not yet consented to the privacy policy?
	 *
	 * @param   JUser|User $user
	 *
	 * @return  bool    Am I allowed to edit this user?
	 */
	private function allowEdit($user)
	{
		if (!$this->isJoomlaPrivacyEnabled())
		{
			return false;
		}

		return !$this->hasUserConsented($user);
	}
}
