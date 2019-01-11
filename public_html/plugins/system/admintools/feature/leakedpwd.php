<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureLeakedpwd extends AtsystemFeatureAbstract
{
	protected $loadOrder = 900;

	public function isEnabled()
	{
		// Protect vs broken host
		if (!function_exists('sha1'))
		{
			return false;
		}

		return ($this->cparams->getValue('leakedpwd', 0) == 1);
	}

	/**
	 * Hooks into the Joomla! models before a user is saved.
	 *
	 * @param   JUser|array     $oldUser  The existing user record
	 * @param   bool            $isNew    Is this a new user?
	 * @param   array           $data     The data to be saved
	 *
	 * @throws  Exception  When we catch a security exception
	 */
	public function onUserBeforeSave($oldUser, $isNew, $data)
	{
		if (!isset($data['password_clear']) || !$data['password_clear'])
		{
			return;
		}

		// This group is allowed to have insecure passwords? If so let's stop here
		if (!$this->checkByGroup($oldUser, $data))
		{
			return;
		}

		// HIBP database searches for the first 5 chars, if the rest of the hash is in the response body, the password
		// is included in a leaked database
		$hashed = strtoupper(sha1($data['password_clear']));
		$search = substr($hashed, 0, 5);
		$body	= substr($hashed, 5);

		$http = JHttpFactory::getHttp();
		$http->setOption('user-agent', 'admin-tools-pwd-checker');

		try
		{
			$response = $http->get('https://api.pwnedpasswords.com/range/'.$search);
		}
		catch (Exception $e)
		{
			// Do not die if anything wrong happens
			return;
		}

		// This should never happen, but better be safe than sorry
		if ($response->code !== 200)
		{
			return;
		}

		// There's no need to further process the response: if the rest of the hash is inside the body,
		// it means that is an insecure password
		if (strpos($response->body, $body) !== false)
		{
			// Load the component's administrator translation files
			$jlang = JFactory::getLanguage();
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

			throw new Exception(JText::sprintf('COM_ADMINTOOLS_LEAKEDPWD_ERR', $data['password_clear']), '403');
		}
	}

	/**
	 * Given a user group, should we allow insecure passwords?
	 *
	 * @param	JUser|array	$oldUser
	 * @param	array		$data
	 *
	 * @return bool		Should we continue with the check or no?
	 */
	private function checkByGroup($oldUser, $data)
	{
		$groups = $this->cparams->getValue('leakedpwd_groups');

		if (!$groups)
		{
			return false;
		}

		// Ok, here's the situation. If you DON'T change user group, $data['groups'] is empty and we have to check the $oldUser.
		// If you change the groups or create a new user, data['groups'] is populated and we have to check for it
		if (isset($data['groups']) && $data['groups'])
		{
			$user_groups = $data['groups'];
		}
		else
		{
			$user_groups = array();

			if(is_array($oldUser))
			{
				$user_groups = $oldUser['groups'];
			}
			elseif($oldUser instanceof JUser)
			{
				$user_groups = $oldUser->groups;
			}
		}

		// We have the groups, now let's check them
		foreach ($user_groups as $user_group)
		{
			if (in_array($user_group, $groups))
			{
				return true;
			}
		}

		return false;
	}
}
