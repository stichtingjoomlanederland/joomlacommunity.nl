<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;

defined('_JEXEC') || die;

class AtsystemFeatureNofesalogin extends AtsystemFeatureAbstract
{
	protected $loadOrder = 900;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (!$this->container->platform->isFrontend())
		{
			return false;
		}

		if ($this->cparams->getValue('nofesalogin', 0) != 1)
		{
			return false;
		}

		return true;
	}

	public function onUserLogin($user, $options)
	{
		$instance = $this->getUserObject($user, $options);

		$isSuperAdmin = $instance->authorise('core.admin');

		if (!$isSuperAdmin)
		{
			return true;
		}

		// Is this a Joomla! 3.9+ installation with a user who's not yet provided consent?
		if ($this->isJoomlaPrivacyEnabled())
		{
			$userID     = UserHelper::getUserId($user['username']);
			$userObject = Factory::getUser($userID);

			if (!$this->hasUserConsented($userObject))
			{
				return true;
			}
		}

		$newopts = [];
		$this->app->logout($instance->id, $newopts);

		// Since Joomla! 2.5.5 you have to close the session before throwing an error, otherwise the user isn't
		// logged out.
		$this->container->session->close();

		// Throw error
		throw new Exception(Text::_('JGLOBAL_AUTH_ACCESS_DENIED'), 403);
	}

	function &getUserObject($user, $options = [])
	{
		$instance = new User();

		if ($id = intval(UserHelper::getUserId($user['username'])))
		{
			$instance->load($id);

			return $instance;
		}

		$config           = ComponentHelper::getParams('com_users');
		$defaultUserGroup = $config->get('new_usertype', 2);

		$instance->set('id', 0);
		$instance->set('name', $user['fullname']);
		$instance->set('username', $user['username']);
		$instance->set('email', $user['email']); // Result should contain an email (check)
		$instance->set('usertype', 'deprecated');
		$instance->set('groups', [$defaultUserGroup]);

		return $instance;
	}
}
