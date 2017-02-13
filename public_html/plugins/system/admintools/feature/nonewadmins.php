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

		$enabled = $fromBackend && $this->helper->isBackend();
		$enabled |= $fromFrontend && $this->helper->isFrontend();

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

		$user = JFactory::getUser((int)$jform['id']);

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
			$reason = $this->helper->isBackend() ? 'nonewadmins' : 'nonewfrontendadmins';

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
	 * @param   JUser  $oldUser  The existing user record
	 * @param   bool   $isNew    Is this a new user?
	 * @param   array  $data     The data to be saved
	 *
	 * @throws  Exception  When we catch a security exception
	 */
	public function onUserBeforeSave($oldUser, $isNew, $data)
	{
		$isAdmin = $this->hasAdminGroup($data['groups']);

		if ($isAdmin)
		{
			// Get the correct reason (was the user being created in front- or back-end)?
			$reason = $this->helper->isBackend() ? 'nonewadmins' : 'nonewfrontendadmins';

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