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

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewMigrators extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.migrators');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		parent::display($tpl);
	}

	/**
	 * Displays kunena migration form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function kunena()
	{
		$this->checkAccess('discuss.manage.migrators');

		$exists = JFile::exists(JPATH_ROOT . '/components/com_kunena/kunena.php');

		$this->title('COM_EASYDISCUSS_MIGRATORS_KUNENA');
		$this->desc('COM_EASYDISCUSS_MIGRATORS_KUNENA_DESC');

		$this->set('exists', $exists);

		parent::display('migrators/kunena');
	}

	/**
	 * Displays the jomsocial groups migration form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function jomsocial()
	{
		$this->checkAccess('discuss.manage.migrators');

		$exists = JFile::exists(JPATH_ROOT . '/components/com_community/community.php');
		$this->set('exists', $exists);

		$this->title('COM_EASYDISCUSS_MIGRATORS_JOMSOCIAL_GROUPS');
		$this->desc('COM_EASYDISCUSS_MIGRATORS_JOMSOCIALGROUPS_DESC');

		parent::display('migrators/jomsocial');
	}

	/**
	 * Displays the vbulletin migration form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function vbulletin()
	{
		$this->checkAccess('discuss.manage.migrators');

		$this->title('COM_EASYDISCUSS_MIGRATORS_VBULLETIN');
		$this->desc('COM_EASYDISCUSS_MIGRATORS_VBULLETIN_DESC');

		parent::display('migrators/vbulletin');
	}

	public function cpolls()
	{
		$this->checkAccess('discuss.manage.migrators');

		$this->set('installed', $this->communityPollsExists());

		parent::display('migrators/cpolls');
	}

	/**
	 * Displays the migration form for discussions component
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function discussions()
	{
		$this->checkAccess('discuss.manage.migrators');

		$this->title('Discussions');
		$this->desc('Migrate forum posts from your Discussion extension.');

		$exists = JFile::exists(JPATH_ADMINISTRATOR . '/components/com_discussions/discussions.php');
		$this->set('exists', $exists);

		parent::display('migrators/discussions');
	}

	public function communityPollsExists()
	{
		return JFile::exists(JPATH_ROOT . '/administrator/components/com_communitypolls/communitypolls.xml');
	}

	public function registerToolbar()
	{
		JToolBarHelper::custom('migrators.purge', 'delete.png', 'delete_f2.png', JText::_('COM_DISCUSS_PURGE_HISTORY') , false);
	}
}
