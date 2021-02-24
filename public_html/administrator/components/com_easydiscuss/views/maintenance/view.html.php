<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussViewMaintenance extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.maintenance');

		// Set page attributes
		$this->title('COM_EASYDISCUSS_MAINTENANCE_TITLE_SCRIPTS');
		$this->desc('COM_EASYDISCUSS_MAINTENANCE_TITLE_SCRIPTS_DESC');

		if ($this->input->get('success', 0, 'int')) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_MAINTENANCE_SUCCESSFULLY_EXECUTED_SCRIPT'), 'success');
		}

		JToolbarHelper::custom('maintenance.form', 'refresh', '', JText::_('COM_EASYDISCUSS_MAINTENANCE_EXECUTE_SCRIPTS'));

		// filters
		$version = $this->app->getUserStateFromRequest('com_easydiscuss.maintenance.filter_version', 'filter_version', 'all', 'cmd');

		$order = $this->app->getUserStateFromRequest('com_easydiscuss.maintenance.filter_order', 'filter_order', 'version', 'cmd');
		$orderDirection	= $this->app->getUserStateFromRequest('com_easydiscuss.maintenance.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		$versions = array();

		$model = ED::model('Maintenance');
		$model->setState('version', $version);
		$model->setState('ordering', $order);
		$model->setState('direction', $orderDirection);

		$scripts = $model->getItems();
		$pagination = $model->getPagination();

		$versions = $model->getVersions();

		$this->set('version', $version);
		$this->set('scripts', $scripts);
		$this->set('versions', $versions);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);
		$this->set('pagination', $pagination);

		parent::display('maintenance/default');
	}

	public function form($tpl = null)
	{
		$this->checkAccess('discuss.manage.maintenance');

		$cids = $this->input->get('cid', array(), 'var');

		$scripts = ED::model('Maintenance')->getItemByKeys($cids);

		$this->set('scripts', $scripts);

		parent::display('maintenance/form');
	}

	/**
	 * Displays the theme installer form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function sync($tpl = null)
	{
		// Check for access
		$this->checkAccess('discuss.manage.maintenance');

		$this->title('COM_ED_MAINTENANCE_TITLE_SYNC');
		$this->desc('COM_ED_MAINTENANCE_TITLE_SYNC_DESC');

		parent::display('maintenance/sync');
	}

	/**
	 * Assign acl rules to existing Joomla groups
	 *
	 * @since	5.0.0
	 * @access	private
	 */
	private function assignACL()
	{
		// Get the db
		$db = ED::db();

		// Retrieve all user groups from the site
		$query = array();
		$query[] = 'SELECT a.' . $db->nameQuote('id') . ', a.' . $db->nameQuote('title') . ' AS ' . $db->nameQuote('name') . ', COUNT(DISTINCT b.' . $db->nameQuote('id') . ') AS ' . $db->nameQuote('level');
		$query[] = ', GROUP_CONCAT(b.' . $db->nameQuote('id') . ' SEPARATOR \',\') AS ' . $db->nameQuote('parents');
		$query[] = 'FROM ' . $db->nameQuote('#__usergroups') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->nameQuote('#__usergroups') . ' AS b';
		$query[] = 'ON a.' . $db->nameQuote('lft') . ' > b.'  . $db->nameQuote('lft');
		$query[] = 'AND a.' . $db->nameQuote('rgt') . ' < b.' . $db->nameQuote('rgt');
		$query[] = 'GROUP BY a.' . $db->nameQuote('id');
		$query[] = 'ORDER BY a.' . $db->nameQuote('lft') . ' ASC';

		$query = implode(' ', $query);
		$db->setQuery($query);

		// Default values
		$groups = array();
		$result = $db->loadColumn();

		// Get a list of default acls
		$query = array();
		$query[] = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__discuss_acl');
		$query[] = 'ORDER BY ' . $db->nameQuote('id') . ' ASC';

		$query = implode(' ', $query);
		$db->setQuery($query);

		// Get those acls
		$installedAcls = $db->loadColumn();

		// Default admin groups
		$adminGroups = array(7, 8);

		if (!empty($result)) {

			foreach ($result as $id) {

				$id = (int) $id;

				// Every other group except admins and super admins should only have restricted access
				if (in_array($id, $adminGroups)) {
					$groups[$id] = $installedAcls;
				} else {

					$allowedAcl = array();

					// Default guest / public group
					if ($id == 1 || $id == 9) {
						$allowedAcl = array(1, 2, 3, 4);
					} else {
						// other groups
						$allowedAcl = array(1, 2, 3, 4, 25, 26, 30);
					}

					$groups[$id] = $allowedAcl;
				}
			}
		}

		// Go through each groups now
		foreach ($groups as $groupId => $acls) {

			// Now we need to insert the acl rules
			$query = array();
			$insertQuery = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_acl_group');
			$query[] = 'WHERE ' . $db->nameQuote('content_id') . '=' . $db->Quote($groupId);
			$query[] = 'AND ' . $db->nameQuote('type') . '=' . $db->Quote('group');

			$query = implode(' ', $query);

			$db->setQuery($query);
			$exists = $db->loadResult() > 0 ? true : false;

			// Reinitialize the query again.
			$query = 'INSERT INTO ' . $db->nameQuote('#__discuss_acl_group') . ' (' . $db->nameQuote('content_id') . ',' . $db->nameQuote('acl_id') . ',' . $db->nameQuote('status') . ',' . $db->nameQuote('type') . ') VALUES';

			if (!$exists) {

				foreach ($acls as $acl) {
					$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($acl) . ',' . $db->Quote('1') . ',' . $db->Quote('group') . ')';
				}

				//now we need to get the unassigend acl and set it to '0';
				$disabledACLs = array_diff($installedAcls, $acls);

				if ($disabledACLs) {
					foreach ($disabledACLs as $disabledAcl) {
						$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($disabledAcl) . ',' . $db->Quote('0') . ',' . $db->Quote('group') . ')';
					}
				}

			} else {

				// Get a list of acl that is already associated with the group
				$sub = array();
				$sub[] = 'SELECT ' . $db->nameQuote('acl_id') . ' FROM ' . $db->nameQuote('#__discuss_acl_group');
				$sub[] = 'WHERE ' . $db->nameQuote('content_id') . '=' . $db->Quote($groupId);
				$sub[] = 'AND ' . $db->nameQuote('type') . '=' . $db->Quote('group');

				$sub = implode(' ', $sub);
				$db->setQuery($sub);

				$existingGroupAcl = $db->loadColumn();

				// Perform a diff to see which acl rules are missing
				$diff = array_diff($existingGroupAcl, $installedAcls);

				// If there's a difference,
				if ($diff) {
					foreach ($diff as $aclId) {

						$value = 0;

						if (in_array($aclId, $acls)) {
							$value = 1;
						}

						$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($aclId) . ',' . $db->Quote($value) . ',' . $db->Quote('group') . ')';
					}
				}
			}

			// Only run this when there is something to insert
			if ($insertQuery) {
				$insertQuery = implode(',', $insertQuery);
				$query .= $insertQuery;

				$db->setQuery($query);
				$db->Query();
			}
		}

		return true;
	}

	/**
	 * Displays the theme installer form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function syncAcl($tpl = null)
	{
		$db = ED::db();
		
		// First, remove all records from the acl table.
		$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_acl');
		$db->setQuery($query);
		$db->Query();

		// Get the list of acl
		$contents = file_get_contents(DISCUSS_ADMIN_ROOT . '/defaults/acl.json');

		$acls = json_decode($contents);

		foreach ($acls as $acl) {

			$query = array();
			$query[] = 'INSERT INTO ' . $db->nameQuote('#__discuss_acl') . '(' . $db->nameQuote('id') . ',' . $db->nameQuote('action') . ',' . $db->nameQuote('group') . ',' . $db->nameQuote('description') . ',' . $db->nameQuote('public') . ',' . $db->nameQuote('default') . ',' . $db->nameQuote('published') . ')';
			$query[] = 'VALUES(' . $db->Quote($acl->id) . ',' . $db->Quote($acl->action) . ',' . $db->Quote($acl->group) . ',' . $db->Quote($acl->desc) . ',' . $db->Quote($acl->public) . ',' . $db->Quote($acl->default) . ',' . $db->Quote($acl->published) . ')';
			$query = implode(' ', $query);

			$db->setQuery($query);
			$db->Query();
		}

		// Once the acl is initialized, we need to create default values for all the existing groups on the site.
		$this->assignACL();
	}


	/**
	 * Displays the theme installer form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function database($tpl = null)
	{
		// Check for access
		$this->checkAccess('discuss.manage.maintenance');

		$this->title('COM_EASYDISCUSS_MAINTENANCE_TITLE_DATABASE');
		$this->desc('COM_EASYDISCUSS_MAINTENANCE_TITLE_DATABASE_DESC');

		parent::display('maintenance/database');
	}

	public function registerToolbar()
	{
		JToolBarHelper::title(JText::_('COM_EASYDISCUSS_MAINTENANCE_TITLE'), 'maintenance');
	}
}
