<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Date\Date;

defined('_JEXEC') or die;

/**
 * Disable or force a password reset on obsolete administrators (backend users who have not logged into the site for a
 * very long time)
 *
 * WAF configuration parameters:
 * disableobsoleteadmins            Is this feature enabled? Default: 0.
 * disableobsoleteadmins_freq       How often to run this feature [minutes]. Default: 60.
 * disableobsoleteadmins_groups     Which user groups to apply to? Default: empty (all groups)
 * disableobsoleteadmins_maxdays    Minimum time since last login to trigger this feature [days]. Default: 90
 * disableobsoleteadmins_action     Action to take (block|reset). Default: reset
 * disableobsoleteadmins_protected  Protected users
 *
 * @since       5.3.0
 */
class AtsystemFeatureDisableobsoleteadmins extends AtsystemFeatureAbstract
{
	protected $loadOrder = 100;

	/**
	 * WAF settings key prefix for this feature
	 *
	 * @var   string
	 * @since 5.3.0
	 */
	protected $settingsKey = 'disableobsoleteadmins';

	/**
	 * If the user has not logged in for at least this many days we are going to block / force reset their password.
	 *
	 * @var   int
	 * @since 5.3.0
	 */
	protected $maxDays = 0;

	/**
	 * When saving a user who was previously blocked, undoing the block, I have to update their last visit date to today
	 * so they don't get auto-blocked again. Joomla! does not let me modify the data before a user is saved to the
	 * database. What I do instead is intercept the onBeforeSave events, create a list of the users who need to be
	 * modified and then apply these changes by capturing the onUserAfterSave event for this user. The problem is that
	 * by doing so I am triggering yet again the onUserBeforeSave which would make me enter an infinite loop. This array
	 * lets me keep track of the user IDs I am fiddling with so I don't end up in an infinite loop. It's an array
	 * because due to the way user plugins work I *might* end up in a recursive update situation.
	 *
	 * @var   array
	 * @since 5.3.0
	 */
	protected $toUpdateUsers = [];

	/**
	 * This is part of the solution described in the $toUpdateUsers above. This array keeps track of the User IDs I have
	 * already started processing onUserAfterSave so I don't process them again.
	 *
	 * @var   array
	 * @since 5.3.0
	 */
	protected $updatedUsers = [];

	/**
	 * Cache of all the user groups known to Joomla
	 *
	 * @var   array
	 * @since 5.3.0
	 */
	protected $allJoomlaUserGroups = [];

	/**
	 * Is this feature enabled?
	 *
	 * @return  bool
	 *
	 * @since   5.3.0
	 */
	public function isEnabled()
	{
		if ($this->cparams->getValue($this->settingsKey, 0) != 1)
		{
			return false;
		}

		$this->maxDays = $this->cparams->getValue($this->settingsKey . '_maxdays', 90);

		if ($this->maxDays <= 0)
		{
			return false;
		}

		return true;
	}

	/**
	 * Runs as soon as the application has finished initializing, before it routes to a component. We will run our
	 * feature at most every disableobsoleteadmins_freq minutes (default: every 60 minutes)
	 *
	 * @throws  Exception
	 *
	 * @since   5.3.0
	 */
	public function onAfterInitialise()
	{
		$minutes = $this->getRunFrequency();

		$lastJob = $this->getTimestamp($this->settingsKey);
		$nextJob = $lastJob + $minutes * 60;

		$now = new Date();

		if ($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp($this->settingsKey);
			$this->disableObsoleteAdmins();
		}
	}

	/**
	 * Prevent automatic blocking of a backend user manually unblocked by an admin user.
	 *
	 * Presumably one of your users got blocked and they asked you to manually reset their password because they can't
	 * figure out the password reset instructions. If you edit them and remove the forced password reset / user block
	 * from their user account they will be automatically blocked again by this feature. This happens because their
	 * last visit date is before the configured max days threshold since they haven't actually logged in yet! We need to
	 * catch that case and update their last visit day to today to prevent blocking them all over again.
	 *
	 * However, Joomla! only allows us to see data onUserBeforeSave, not update them. Therefore I am using the
	 * onUserBeforeSave event to find out which user accounts are being saved and which need fiddling with per above.
	 * Then I used onUserAfterSave to update their lastVisitDate.
	 *
	 * @param   JUser|array $oldUser The existing user record
	 * @param   bool        $isNew   Is this a new user?
	 * @param   array       $data    The data to be saved
	 *
	 * @throws  Exception  When we catch a security exception
	 */
	public function onUserBeforeSave($oldUser, $isNew, $data)
	{
		// I only care about editing users from the backend
		if ($this->container->platform->isFrontend())
		{
			return;
		}

		// I only care about editing existing users
		if ($isNew)
		{
			return;
		}

		// I don't care if you are editing yourself
		if ($oldUser['id'] == $this->container->platform->getUser()->id)
		{
			return;
		}

		// Do not process the user I am already updating after save.
		if (in_array($oldUser['id'], $this->toUpdateUsers))
		{
			return;
		}

		// Do not process the user I have already updated after save.
		if (in_array($oldUser['id'], $this->updatedUsers))
		{
			return;
		}

		$action = $this->cparams->getValue($this->settingsKey . '_action', 'reset') == 'block' ? 'block' : 'reset';

		switch ($action)
		{
			case 'block':
				// If the user wasn't blocked I have nothing to do
				if ($oldUser['block'] == 0)
				{
					return;
				}

				// If you didn't change the user block status I have nothing to do
				if ($data['block'] == 1)
				{
					return;
				}
				break;

			case 'reset':
			default:
				// If the user wasn't required to password reset I have nothing to do
				if ($oldUser['requireReset'] == 0)
				{
					return;
				}

				// If you didn't change the user's required password reset status I have nothing to do
				if ($data['requireReset'] == 1)
				{
					return;
				}
				break;
		}

		// You are possibly editing a user I previously disabled automatically. Is this REALLY the case?
		if ($oldUser['lastvisitDate'] != $this->db->getNullDate())
		{
			$now       = Date::getInstance();
			$lastLogin = Date::getInstance($oldUser['lastvisitDate']);
			$diff      = $now->diff($lastLogin, true);

			// If the last login was within the allowed number of days you are editing a user I must NOT touch.
			if ($diff->days <= $this->maxDays)
			{
				return;
			}
		}

		// Mark this user as in need for post-save update
		$this->toUpdateUsers[] = $oldUser['id'];
	}

	/**
	 * Part of the automatic update of manually unblocked users, as explained onUserBeforeSave.
	 *
	 * @param   array  $data         The user data saved to the database
	 * @param   bool   $isNew        Was that a new user?
	 * @param   bool   $result       Did the save succeed?
	 * @param   string $errorMessage The last error message while saving the user.
	 *
	 *
	 * @since   5.3.0
	 */
	public function onUserAfterSave($data, $isNew, $result, $errorMessage)
	{
		// I don't care about new users
		if ($isNew)
		{
			return;
		}

		// I don't care about failed saves
		if (!$result)
		{
			return;
		}

		// Get the user ID
		$userID = $data['id'];

		// Do not process the user I have already updated after save.
		if (in_array($userID, $this->updatedUsers))
		{
			return;
		}

		// Do not process a user UNLESS I have marked them as in need for an update.
		if (!in_array($userID, $this->toUpdateUsers))
		{
			return;
		}

		// Mark the user as having their last visit date updated
		$this->updatedUsers[] = $userID;

		// Update the last visit date to today
		$user                = $this->container->platform->getUser($userID);
		$user->lastvisitDate = Date::getInstance()->toSql();
		$user->save(true);
	}

	/**
	 * Find users who belong in the configured backend user groups and who have not logged in for at least the
	 * configured number of days. Then take the configured action against them (force password reset or block them).
	 *
	 * @since   5.3.0
	 * @throws  Exception
	 */
	protected function disableObsoleteAdmins()
	{
		// Get applicable user groups
		$groups = $this->getBackendUserGroups();

		if (empty($groups))
		{
			return;
		}

		// Get all applicable users
		$users = $this->getUsersByGroups($groups);

		// No users? Nothing to do, then.
		if (empty($users))
		{
			return;
		}

		// Remove "protected" users from this list
		$users = array_unique($users);
		$users = $this->removeProtectedUsers($users);

		// No users left after this operation? Nothing to do, then.
		if (empty($users))
		{
			return;
		}

		asort($users);

		// Get the login date to trigger this feature
		$now      = Date::getInstance();
		$interval = new DateInterval(sprintf('P%dD', $this->maxDays));
		$then     = $now->sub($interval)->toSql();

		// Have any of these users not logged in for a while?
		try
		{
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select([$db->qn('id')])
				->from($db->qn('#__users'))
				->where($db->qn('id') . ' IN (' . implode(', ', array_map([$db, 'q'], $users)) . ')')
				->where($db->qn('block') . ' = ' . $db->q(0))
				->where($db->qn('requireReset') . ' = ' . $db->q(0))
				->where($db->qn('lastvisitDate') . ' <= ' . $db->q($then));

			$actionUsers = $db->setQuery($query)->loadColumn(0);
		}
		catch (Exception $e)
		{
			// Database error. ail out.
		}

		asort($actionUsers);

		// Get the applicable action
		$action = $this->cparams->getValue($this->settingsKey . '_action', 'reset') == 'block' ? 'block' : 'reset';

		/**
		 * Am I trying to block all Super Users AND I am not protecting any Super Users THEN I will not block any Super
		 * Users at all.
		 *
		 * Why not do the same if I am forcing a password reset? Because in this case all Super Users can reset their
		 * password over email. No harm done. You don't get locked out of your site.
		 *
		 * Why do this even when I have protected users? Because the user may have chosen to protect non-Super-Users by
		 * accident / because they do not understand the consequences. In this case I have to make sure I am not
		 * blocking any Super Users on their site or they risk getting locked out of it permanently.
		 */
		if ($action == 'block')
		{
			$actionUsers = $this->filterActionableUsersToEnsureRemainingSuperUser($actionUsers);
		}

		// No users to take action against?.
		if (empty($actionUsers))
		{
			return;
		}

		// Apply the action
		$query = $db->getQuery(true)
			->update($db->qn('#__users'))
			->where($db->qn('id') . ' IN (' . implode(', ', array_map([$db, 'q'], $actionUsers)) . ')');

		switch ($action)
		{
			case 'block':
				$query->set($db->qn('block') . ' = ' . $db->q(1));
				break;

			case 'reset':
			default:
				$query->set($db->qn('requireReset') . ' = ' . $db->q(1));
				break;
		}

		$db->setQuery($query)->execute();
	}

	/**
	 * Returns all Joomla! user groups
	 *
	 * @return  array
	 *
	 * @since   5.3.0
	 */
	protected function getAllJoomlaUserGroups()
	{
		if (empty($this->allJoomlaUserGroups))
		{
			// Get all groups
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select([$db->qn('id')])
				->from($db->qn('#__usergroups'));

			$this->allJoomlaUserGroups = $db->setQuery($query)->loadColumn(0);

			// This should never happen (unless your site is very dead, in which case I feel terribly sorry for you...)
			if (empty($this->allJoomlaUserGroups))
			{
				$this->allJoomlaUserGroups = [];
			}
		}

		return $this->allJoomlaUserGroups;
	}

	/**
	 * Get the user groups configured by the user, filtered by those which really have backend access. If no groups are
	 * configured we will use all groups with backend access.
	 *
	 * @since   5.3.0
	 */
	protected function getBackendUserGroups()
	{
		// Get the configured groups
		$groups = $this->cparams->getValue($this->settingsKey . '_groups', []);
		$groups = is_string($groups) ? explode(',', trim($groups)) : $groups;
		$groups = array_filter($groups, function ($group) {
			return (int) trim($group) != 0;
		});

		// No groups? Assume we're to look into all Joomla! user groups.
		if (empty($groups))
		{
			$groups = $this->getAllJoomlaUserGroups();
			// Filter the configured user groups by those with backend access
			$groups = array_filter($groups, [$this, 'isBackendAccessGroup']);
		}

		return $groups;

	}

	/**
	 * Remove the protected users from the given $users list and return the remaining users
	 *
	 * @param   array $users The users list to filter
	 *
	 * @return  array  The filtered list
	 *
	 * @since   5.3.0
	 */
	protected function removeProtectedUsers(array $users)
	{
		$protected = $this->getProtectedUsers();

		if (empty($protected))
		{
			return $users;
		}

		return array_diff($users, $protected);
	}

	/**
	 * Filter the list of actionable users in a way that ensures at least one Super User will remain active on the site
	 *
	 * @param   array $actionableUsers The list of actionable users to filter
	 *
	 * @return  array  The filtered list
	 *
	 * @since   5.3.0
	 */
	protected function filterActionableUsersToEnsureRemainingSuperUser($actionableUsers)
	{
		$protected  = $this->getProtectedUsers();
		$superUsers = $this->getSuperUsers();

		// If I have any protected Super Users bail out; a Super User is guaranteed to exist on the site.
		$protectedSuper = array_intersect($protected, $superUsers);

		if (count($protectedSuper))
		{
			return $actionableUsers;
		}

		// Remove Super Users from list of blocked users
		return array_diff($actionableUsers, $superUsers);
	}

	/**
	 * Return the user IDs of all active (non-blocked) Super Users on the site.
	 *
	 * @return  array
	 *
	 * @since   5.3.0
	 */
	protected function getSuperUsers()
	{
		// Get the Super User groups
		$groups          = $this->getAllJoomlaUserGroups();
		$superUserGroups = array_filter($groups, function ($group) {
			return JAccess::checkGroup($group, 'core.admin', 1);
		});

		// Get all Super Users
		$superUsers = $this->getUsersByGroups($superUserGroups);
		$superUsers = array_unique($superUsers);

		// Return only active (non-blocked) Super User account IDs
		return array_filter($superUsers, function ($userID) {
			return $this->container->platform->getUser($userID)->block == 0;
		});
	}

	/**
	 * Get the protected users' IDs
	 *
	 * @return   int[]
	 *
	 * @since    5.3.0
	 */
	protected function getProtectedUsers()
	{
		$protected = $this->cparams->getValue($this->settingsKey . '_protected', []);
		$protected = is_string($protected) ? explode(',', trim($protected)) : $protected;
		$protected = array_filter($protected, function ($userID) {
			return (int) trim($userID) != 0;
		});

		return $protected;
	}

	/**
	 * Returns all user IDs belonging to any of the group IDs specified.
	 *
	 * @param   array $groups List of all user group IDs we are interested in
	 *
	 * @return  array
	 *
	 * @since   5.3.0
	 */
	protected function getUsersByGroups(array $groups)
	{
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select([$db->qn('user_id')])
			->from($db->qn('#__user_usergroup_map'))
			->where($db->qn('group_id') . ' IN(' . implode(',', array_map(function ($group) use ($db) {
					return $db->q(trim($group));
				}, $groups)) . ')');
		$ret   = $db->setQuery($query)->loadColumn(0);

		if (empty($ret))
		{
			return [];
		}

		return $ret;
	}

	/**
	 * Return the frequency [minutes] for running this feature.
	 *
	 * @return  int
	 *
	 * @since   5.3.0
	 */
	protected function getRunFrequency()
	{
		$minutes = (int) $this->cparams->getValue($this->settingsKey . '_freq', 60);

		if ($minutes <= 0)
		{
			$minutes = 60;
		}

		return $minutes;
	}
}
