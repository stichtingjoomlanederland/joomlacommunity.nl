<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
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
		if (!$this->helper->isBackend())
		{
			return false;
		}

		return ($this->cparams->getValue('nonewadmins', 0) == 1);
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
		}

		if ($isAdmin)
		{
			$jlang = JFactory::getLanguage();
			$jlang->load('joomla', JPATH_ROOT, 'en-GB', true);
			$jlang->load('joomla', JPATH_ROOT, $jlang->getDefault(), true);
			$jlang->load('joomla', JPATH_ROOT, null, true);

			throw new Exception(JText::_('JGLOBAL_AUTH_ACCESS_DENIED'), '403');
		}
	}
}