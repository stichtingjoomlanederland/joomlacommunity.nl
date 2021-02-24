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

class EasyDiscussViewUsers extends EasyDiscussAdminView
{
	/**
	 * Renders the user's listing
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function browse($tpl = null)
	{
		$browseFunction = $this->input->get('browseFunction', 'selectUser', 'string');

		$categoryId = $this->input->get('categoryId', 0, 'int');
		$moderatorOnly = $this->input->get('moderator', 0, 'int');

		$url = JURI::root() . 'administrator/index.php?option=com_easydiscuss&view=users&tmpl=component&browse=1&browsefunction=' . $browseFunction;

		if ($moderatorOnly && $categoryId) {
			$url .= '&moderator=1&category_id=' . $categoryId; 
		}

		$theme = ED::themes();
		$theme->set('url', $url);

		$output = $theme->output('admin/users/dialog');

		return $this->ajax->resolve($output);
	}

	/**
	 * Browses for badge to be assigned to user
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function assignBadge()
	{
		$userIds = $this->input->get('cid', array(), 'array');

		if ($userIds) {
			$userIds = implode(',', $userIds);
			$userIds = base64_encode($userIds);
		}

		$url = JURI::root() . 'administrator/index.php?option=com_easydiscuss&amp;view=badges&amp;tmpl=component&amp;browse=1&amp;browseFunction=insertBadge&amp;userIds=' . $userIds;

		$theme = ED::themes();
		$theme->set('url', $url);

		$output = $theme->output('admin/users/dialogs/browse.badge');

		return $this->ajax->resolve($output);
	}	
}