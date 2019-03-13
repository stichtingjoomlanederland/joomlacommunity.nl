<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Date\Date;

defined('_JEXEC') or die;

if (!class_exists('AtsystemFeatureDisableobsoleteadmins'))
{
	require_once 'disableobsoleteadmins.php';
}

/**
 * Disable temporary Super User accounts
 *
 * @since       5.3.0
 */
class AtsystemFeatureTempsuperuser extends AtsystemFeatureDisableobsoleteadmins
{
	protected $loadOrder = 102;

	/**
	 * WAF settings key prefix for this feature
	 *
	 * @var   string
	 * @since 5.3.0
	 */
	protected $settingsKey = 'tempsuperuser';

	/**
	 * This feature is always enabled
	 *
	 * @return  bool
	 *
	 * @since   5.3.0
	 */
	public function isEnabled()
	{
		return true;
	}

	/**
	 * This feature always runs at most once an hour
	 *
	 * @return  int
	 *
	 * @since   5.3.0
	 */
	protected function getRunFrequency()
	{
		return 60;
	}

	/**
	 * Unlike prevent login of forgotten Super Users, this feature does not require handling of changing the user status
	 * through Joomla's com_users.
	 *
	 * @param   JUser|array $oldUser The existing user record
	 * @param   bool        $isNew   Is this a new user?
	 * @param   array       $data    The data to be saved
	 */
	public function onUserBeforeSave($oldUser, $isNew, $data)
	{
		return;
	}

	/**
	 * Unlike prevent login of forgotten Super Users, this feature does not require handling of changing the user status
	 * through Joomla's com_users.
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
		return;
	}

	/**
	 * This feature does not define any protected users (it defines unprotected users instead).
	 *
	 * @return  array|int[]
	 *
	 * @since   5.3.0
	 */
	protected function getProtectedUsers()
	{
		return [];
	}

	/**
	 * Implements automatic blocking of temporary Super Users after they are expired
	 *
	 * @since  5.3.0
	 */
	protected function disableObsoleteAdmins()
	{
		try
		{
			// Find temporary Super Users who are expired
			$db      = $this->db;
			$now     = new Date();
			$query   = $db->getQuery(true)
				->select([
					$db->qn('user_id'),
				])->from($db->qn('#__admintools_tempsupers'))
				->where($db->qn('expiration') . ' <= ' . $db->q($now->toSql()));
			$userIDs = $db->setQuery($query)->loadColumn(0);
		}
		catch (Exception $e)
		{
			// Database error. ail out.
		}

		// No expired Super Users? Bail out.
		if (empty($userIDs))
		{
			return;
		}

		/**
		 * There's a reason I decided to comment out this block. This was inherited from disabling obsolete super users
		 * which could potentially cause all Super Users to be disabled.
		 *
		 * However, temporary Super Users has three protections built into the interface to prevent that:
		 *
		 * - You cannot make yourself a temporary Super User
		 * - A temporary Super User cannot manage temporary Super Users, therefore cannot make another SU temporary.
		 * - If you add an existing Super User they have to be already disabled.
		 *
		 * This means that you always have an active, non-temporary  Super User on the site, no matter what.
		 */
		// ======
		// Make sure there will be at least one remaining Super User after I am done
		// $userIDs = $this->filterActionableUsersToEnsureRemainingSuperUser($userIDs);
		// ======

		// No actionable Super Users? Bail out.
		if (empty($userIDs))
		{
			return;
		}

		$userIDListForDatabase = implode(', ', array_map([$db, 'q'], $userIDs));

		// Block the users
		$query   = $db->getQuery(true)
			->update($db->qn('#__users'))
			->where($db->qn('id') . ' IN (' . $userIDListForDatabase . ')')
			->set($db->qn('block') . ' = ' . $db->q(1));

		$db->setQuery($query)->execute();

		// Remove the users from the #__admintools_tempsupers table as well
		$query  = $db->getQuery(true)
			->delete($db->qn('#__admintools_tempsupers'))
			->where($db->qn('user_id') . ' IN (' . $userIDListForDatabase . ')');

		$db->setQuery($query)->execute();
	}
}
