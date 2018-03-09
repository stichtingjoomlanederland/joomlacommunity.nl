<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/**
 * Keep track of Super Users on the site and send an email when users are added. Optionally automatically block these
 * new Super Users.
 */
class AtsystemFeatureSuperuserslist extends AtsystemFeatureAbstract
{
	protected $loadOrder = 998;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		/**
		 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		 * A Short History Of How This Feature Ended Up Disabled By Default
		 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		 *
		 * Despite this feature working just fine, we found out that it's a constant source of support request for
		 * reasons unrelated to its performance or reliability. It boils down to:
		 *
		 * - Badly written third party software, some of it running outside Joomla!, will create from scratch or afresh
		 *   Super User accounts silently. This is EXACTLY the problem this feature is supposed to catch and it does.
		 *   However users don't perceive that ugly and dangerous third party hack as a problem and instead believe that
		 *   it's Admin Tools fault for warning them when they have not been subjectively hacked (in fact, the machine
		 *   has no way to determine that what just happened is not malicious BECAUSE THAT'S EXACTLY WHAT AN EVIL HACKER
		 *   WOULD DO TO PWN YOUR SITE).
		 *
		 * - People forget that they have disabled Admin Tools when they are creating a Super User, therefore making it
		 *   impossible for AT to know if the new Super User is legit or an evil implant. AT warns them, as they should,
		 *   but they again think it's a bug - despite the feature doing EXACTLY what it is asked to do, i.e. warn for
		 *   any user account created outside the Users editor and / or outside its watch.
		 *
		 * - People use third party extensions either by themselves (obvious) or one which override the backend Users
		 *   page of Joomla! (absolutely not obvious). In this case the created Super User is indeed created outside the
		 *   backend Users page of Joomla! so Admin Tools correctly warns them. Once more, people perceive it as a bug
		 *   in Admin Tools.
		 */

		return ($this->cparams->getValue('superuserslist', 0) == 1);
	}

	/**
	 * Checks if a backend Super User is saving another Super User account. We have to run this check onAfterRoute since
	 * com_users will perform an immediate redirect upon saving, without hitting onAfterRender. For the same reason the
	 * detected ID of the Super User being saved has to be saved in the session to persist the successive page loads.
	 */
	public function onAfterRoute()
	{
		if (!$this->isBackendSuperUser())
		{
			return;
		}

		// Do I already have session data?
		$safeIDs           = $this->container->platform->getSessionVar('superuserslist.safeids', [], 'com_admintools');
		$isUserSaveOrApply = $this->container->platform->getSessionVar('superuserslist.createnew', null, 'com_admintools');

		if (!is_null($isUserSaveOrApply))
		{
			// Yeah. Let's not overwrite the session data. We shall do that onAfterRender.
			return;
		}

		$safeIDs = $this->getSafeIDs();

		// Get the option and task parameters
		$app               = JFactory::getApplication();
		$option            = $app->input->getCmd('option', 'com_foobar');
		$task              = $app->input->getCmd('task');
		$isUserSaveOrApply = false;

		// Are we using com_user to Save or Save & Close a user?
		if ($option == 'com_users')
		{
			if (in_array($task, ['user.apply', 'user.save']))
			{
				$isUserSaveOrApply = true;
			}
		}

		$this->container->platform->setSessionVar('superuserslist.safeids', $safeIDs, 'com_admintools');
		$this->container->platform->setSessionVar('superuserslist.createnew', $isUserSaveOrApply, 'com_admintools');
	}

	public function onAfterRender()
	{
		// Only run if the current user is a Super User AND we haven't already set a flag
		$currentUser = $this->container->platform->getUser();

		if ($currentUser->guest)
		{
			return;
		}

		if (!$currentUser->authorise('core.admin'))
		{
			return;
		}

		$flag = $this->container->platform->getSessionVar('allowedsuperuser', null, 'com_admintools');

		if ($flag === true)
		{
			return;
		}

		// Get temporary session variables
		$safeIDs           = $this->container->platform->getSessionVar('superuserslist.safeids', [], 'com_admintools');
		$isUserSaveOrApply = $this->container->platform->getSessionVar('superuserslist.createnew', null, 'com_admintools');

		$this->container->platform->unsetSessionVar('superuserslist.safeids', 'com_admintools');
		$this->container->platform->unsetSessionVar('superuserslist.createnew', 'com_admintools');

		// Normalize
		if (empty($safeIDs))
		{
			$safeIDs = [];
		}

		if (empty($isUserSaveOrApply))
		{
			$isUserSaveOrApply = false;
		}

		// If it's not a backend Super User we are going to ignore session variables (they are forged!)
		if (!$this->isBackendSuperUser())
		{
			$safeIDs           = [];
			$isUserSaveOrApply = false;
		}

		// Get the Super User IDs
		$savedSuperUserIDs   = $this->load();
		$superUserGroups     = $this->getSuperUserGroups();
		$currentSuperUserIDs = $this->getUsersInGroups($superUserGroups);

		// Oh, we never had a list of Super Users. Let's fix that.
		if (empty($savedSuperUserIDs))
		{
			$this->save($currentSuperUserIDs);

			return;
		}

		// Do we have new Super Users?
		$newSuperUsers = array_diff($currentSuperUserIDs, $savedSuperUserIDs);
		// Do NOT remove this variable! It catches the case were Super Users are added BUT THEN REMOVED FROM $newSuperUsers WITH array_diff. WE MUST SAVE IN THIS CASE!
		$hasNewSuperUsers  = !empty($newSuperUsers);
		$newSuperUsers     = array_diff($newSuperUsers, $safeIDs);
		$removedSuperUsers = array_diff($savedSuperUserIDs, $currentSuperUserIDs);

		// Detect the case where we have to simply save the list of Super Users and quit (no new or removed SUs)
		$saveListAndQuit = empty($newSuperUsers) && empty($removedSuperUsers);

		/**
		 * Special case: Super User logged in backend creates a new user account that is also a Super User.
		 *
		 * In this case we do not have any safeIDs because the JForm is being submitted with user ID 0. This is normal
		 * since we are creating a new user record, therefore we do not have a user ID yet. We can distinguish this
		 * case from the generic "third party backend extension creates a new user account" by checking the option and
		 * task parameters. If the option is com_users (the Joomla! user management core component) and the task
		 * indicates applying or saving a user we have the special case we need to avoid blocking.
		 */
		if ($this->isBackendSuperUser() && empty($safeIDs) && $isUserSaveOrApply)
		{
			$saveListAndQuit = true;
		}

		if ($saveListAndQuit)
		{
			// In case Super Users ARE added BUT are in the safe IDs list THEN we MUST save the new list!
			if ($hasNewSuperUsers)
			{
				$this->save($currentSuperUserIDs);
			}

			return;
		}

		// If we're here a new Super User was added through means unknown. Notify the admins and block the user.
		$this->sendEmail($newSuperUsers);
		$flag = true;

		foreach ($newSuperUsers as $id)
		{
			$user        = $this->container->platform->getUser($id);
			$user->block = 1;
			$user->save();

			if ($currentUser->id == $id)
			{
				$flag = false;
			}
		}

		$this->container->platform->setSessionVar('allowedsuperuser', $flag, 'com_admintools');

		$currentSuperUserIDs = array_diff($currentSuperUserIDs, $newSuperUsers);
		$newSuperUsers       = [];

		if (!empty($newSuperUsers) || !empty($removedSuperUsers))
		{
			$this->save($currentSuperUserIDs);
		}

		// Is the current user one of the new, bad admins? If so, try to log the out
		if ($flag === false)
		{
			$app = JFactory::getApplication();

			// Try being nice about it
			if (!$app->logout())
			{
				// If being nice about logging you out doesn't work I'm gonna terminate you, with extreme prejudice.
				$app->getSession()->set('user', null);
				$app->getSession()->destroy();
			}
		}
	}

	/**
	 * Save the list of users to the database
	 *
	 * @param   array $userList The list of User IDs
	 *
	 * @return  void
	 */
	private function save(array $userList)
	{
		$db   = $this->container->db;
		$data = json_encode($userList);

		$query = $db->getQuery(true)
		            ->delete($db->quoteName('#__admintools_storage'))
		            ->where($db->quoteName('key') . ' = ' . $db->quote('superuserslist'));
		$db->setQuery($query);
		$db->execute();

		$object = (object) array(
			'key'   => 'superuserslist',
			'value' => $data
		);

		$db->insertObject('#__admintools_storage', $object);
	}

	/**
	 * Load the saved list of Super User IDs from the database
	 *
	 * @return  array
	 */
	private function load()
	{
		$db    = $this->container->db;
		$query = $db->getQuery(true)
		            ->select($db->quoteName('value'))
		            ->from($db->quoteName('#__admintools_storage'))
		            ->where($db->quoteName('key') . ' = ' . $db->quote('superuserslist'));
		$db->setQuery($query);

		$error = 0;

		try
		{
			$jsonData = $db->loadResult();
		}
		catch (Exception $e)
		{
			$error = $e->getCode();
		}

		if (method_exists($db, 'getErrorNum') && $db->getErrorNum())
		{
			$error = $db->getErrorNum();
		}

		if ($error)
		{
			$jsonData = null;
		}

		if (empty($jsonData))
		{
			return [];
		}

		return json_decode($jsonData, true);
	}

	/**
	 * Sends a warning email to the addresses set up to receive security exception emails
	 *
	 * @param   array  $superUsers  The IDs of Super Users added
	 *
	 * @return  void
	 */
	private function sendEmail(array $superUsers)
	{
		if (empty($superUsers))
		{
			// What are you doing here?
			return;
		}

		// Load the component's administrator translation files
		$jlang = JFactory::getLanguage();
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

		// Convert the list of added Super Users
		$htmlUsersList = <<< HTML
<ul>
HTML;

		foreach ($superUsers as $id)
		{
			$user = $this->container->platform->getUser($id);

			$htmlUsersList .= <<< HTML
	<li>
		#$id &ndash; <b>{$user->username}</b> &ndash; {$user->name} &lt;{$user->email}&gt;
	</li>
HTML;

		}

		$htmlUsersList .= <<< HTML
</ul>

HTML;

		// Construct the replacement table
		$substitutions = $this->exceptionsHandler->getEmailVariables('', [
			'[INFO]'      => $htmlUsersList
		]);

		// Let's get the most suitable email template
		$template = $this->exceptionsHandler->getEmailTemplate('superuserslist', true);

		// Got no template, the user didn't published any email template, or the template doesn't want us to
		// send a notification email. Anyway, let's stop here.
		if (!$template)
		{
			return;
		}

		$subject = $template[0];
		$body = $template[1];

		foreach ($substitutions as $k => $v)
		{
			$subject = str_replace($k, $v, $subject);
			$body    = str_replace($k, $v, $body);
		}

		try
		{
			$config = $this->container->platform->getConfig();
			$mailer = JFactory::getMailer();

			$mailfrom = $config->get('mailfrom');
			$fromname = $config->get('fromname');

			$recipients = explode(',', $this->cparams->getValue('emailbreaches', ''));
			$recipients = array_map('trim', $recipients);

			foreach ($recipients as $recipient)
			{
				if (empty($recipient))
				{
					continue;
				}

				// This line is required because SpamAssassin is BROKEN
				$mailer->Priority = 3;

				$mailer->isHtml(true);
				$mailer->setSender(array($mailfrom, $fromname));

				// Resets the recipients, otherwise they will pile up
				$mailer->clearAllRecipients();

				if ($mailer->addRecipient($recipient) === false)
				{
					// Failed to add a recipient?
					continue;
				}

				$mailer->setSubject($subject);
				$mailer->setBody($body);
				$mailer->Send();
			}
		}
		catch (\Exception $e)
		{
			// Joomla! 3.5 and later throw an exception when crap happens instead of suppressing it and returning false
		}
	}

	/**
	 * Get the user groups with Super User privileges
	 *
	 * @return  array
	 */
	private function getSuperUserGroups()
	{
		static $ret = null;

		if (!is_array($ret))
		{
			$db  = $this->container->db;
			$ret = [];

			try
			{
				$query = $db->getQuery(true)
				            ->select($db->qn('rules'))
				            ->from($db->qn('#__assets'))
				            ->where($db->qn('parent_id') . ' = ' . $db->q(0));
				$db->setQuery($query, 0, 1);
				$rulesJSON = $db->loadResult();
			}
			catch (Exception $exc)
			{
				return $ret;
			}

			$rules     = json_decode($rulesJSON, true);
			$rawGroups = $rules['core.admin'];

			if (empty($rawGroups))
			{
				return $ret;
			}

			foreach ($rawGroups as $g => $enabled)
			{
				if (!$enabled)
				{
					continue;
				}

				$ret[] = $g;
			}
		}

		return $ret;
	}

	/**
	 * Get the IDs of users who are members of one or more groups in the $groups list
	 *
	 * @param   array  $groups  The users must be a member of at least one of these groups
	 *
	 * @return  array
	 */
	private function getUsersInGroups(array $groups)
	{
		$db  = $this->container->db;
		$ret = [];
		$groups = array_map(array($db, 'q'), $groups);

		try
		{
			$query = $db->getQuery(true)
			            ->select($db->qn('user_id'))
			            ->from($db->qn('#__user_usergroup_map') . ' AS ' . $db->qn('m'))
			            ->innerJoin($db->qn('#__users') . ' AS ' . $db->qn('u') . 'ON(' .
				            $db->qn('u.id') . ' = ' . $db->qn('m.user_id')
			            . ')')
			            ->where($db->qn('group_id') . ' IN(' . implode(',', $groups) . ')' )
			            ->where($db->qn('block') . ' = ' . $db->q('0') )
						// Don't look only for empty string. Joomla! considers '' and '0' identical and will let you log in!
			            ->where('(' .
							'(' . $db->qn('activation') . ' = ' . $db->q('0') . ') OR ' .
							'(' . $db->qn('activation') . ' = ' . $db->q('') . ')' .
						')')
			;
			$db->setQuery($query);
			$rawUserIDs = $db->loadColumn(0);
		}
		catch (Exception $exc)
		{
			return $ret;
		}

		if (empty($rawUserIDs))
		{
			return $ret;
		}

		return array_unique($rawUserIDs);
	}

	/**
	 * Returns a list of safe Super User IDs. These are the IDs of the Super Users being saved by another Super User in
	 * the backend of the site through com_users.
	 *
	 * @return  array
	 */
	public function getSafeIDs()
	{
		$app = JFactory::getApplication();

		if (!$this->isBackendSuperUser())
		{
			return [];
		}

		// Get the option and task parameters
		$option = $app->input->getCmd('option', 'com_foobar');
		$task   = $app->input->getCmd('task');

		// Not com_users?
		if ($option != 'com_users')
		{
			return [];
		}

		// Special case: unblock with one click. There's no jform here, the ID is passed in the 'cid' query string parameter
		if ($task == 'users.unblock')
		{
			$cid = $app->input->get('cid', [], 'array');

			if (empty($cid))
			{
				return [];
			}

			if (!is_array($cid))
			{
				$cid = [$cid];
			}

			return $cid;
		}

		// Note Save or Save & Close?
		if (!in_array($task, ['user.apply', 'user.save']))
		{
			return [];
		}

		// Get the user IDs from the form
		$jForm = $app->input->get('jform', [], 'array');

		if (!is_array($jForm) || empty($jForm))
		{
			return [];
		}

		// No user ID or group information?
		if (!isset($jForm['groups']) || !isset($jForm['id']))
		{
			return [];
		}

		// Is it a Super User?
		$superUserGroups = $this->getSuperUserGroups();
		$groups          = $jForm['groups'];
		$isSuperUser     = false;

		if (empty($groups))
		{
			return [];
		}

		foreach ($groups as $group)
		{
			if (in_array($group, $superUserGroups))
			{
				$isSuperUser = true;

				break;
			}
		}

		if (!$isSuperUser)
		{
			return [];
		}

		// Get the user ID being saved and return it
		$id = $jForm['id'];

		if (empty($id))
		{
			return [];
		}

		return [$id];
	}

	/**
	 * Are we currently in the backend, with a logged in Super User?
	 *
	 * @return  bool
	 */
	private function isBackendSuperUser()
	{
		$app = JFactory::getApplication();

		// Not a valid application object?
		if (!is_object($app))
		{
			return false;
		}

		$isCMSApp = version_compare(JVERSION, '3.99999.99999', 'gt') ? ($app instanceof \Joomla\CMS\Application\CMSApplication) : $app instanceof JApplicationCms;

		if (!$isCMSApp)
		{
			return false;
		}

		// Are we in the backend?
		$isAdmin = method_exists($app, 'isAdmin') ? $app->isAdmin() : $app->isClient('administrator');

		if (!$isAdmin)
		{
			return false;
		}

		// Not a Super User?
		if (!$this->container->platform->getUser()->authorise('core.admin'))
		{
			return false;
		}

		return true;
	}
} 
