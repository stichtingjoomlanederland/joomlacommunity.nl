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

jimport('joomla.application.component.controller');

class EasyDiscussControllerBadges extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		// Need to explicitly define this in Joomla 3.0
		$this->registerTask('unpublish', 'togglePublish');
		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('assign', 'assign');
	}

	public function assign()
	{
		ED::redirect('index.php?option=com_easydiscuss&view=badges&layout=assign');
	}

	public function add()
	{
		ED::redirect('index.php?option=com_easydiscuss&view=badges&layout=form');
	}

	public function cancel()
	{
		ED::redirect('index.php?option=com_easydiscuss&view=badges');
		return;
	}

	public function remove()
	{
		ED::checkToken();

		$ids = $this->input->get('cid', '', 'array');

		$badge = ED::table('Badges');

		foreach ($ids as $id) {
			$badge->load($id);
			$badge->delete();
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_BADGES_DELETED'), DISCUSS_QUEUE_SUCCESS);
		ED::redirect('index.php?option=com_easydiscuss&view=badges');
	}

	public function togglePublish()
	{
		ED::checkToken();

		// Get the current task
		$task = $this->getTask();
		$ids = $this->input->get('cid', '', 'array');

		$badge = ED::table('Badges');

		foreach ($ids as $id) {
			$badge->load((int) $id);
			$badge->$task();
		}

		$message = $task == 'publish' ? JText::_('COM_EASYDISCUSS_BADGES_PUBLISHED') : JText::_('COM_EASYDISCUSS_BADGES_UNPUBLISHED');

		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);
		ED::redirect('index.php?option=com_easydiscuss&view=badges');
	}

	/**
	 * Method to save a badge
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function save()
	{
		ED::checkToken();

		// Get the current task
		$task = $this->getTask();

		$id = $this->input->get('id', 0, 'int');
		$isNew = $id ? false : true;

		$redirect = 'index.php?option=com_easydiscuss&view=badges';

		// Load the badge.
		$badge = ED::table('Badges');
		$badge->load($id);

		$oldTitle = $badge->title;

		$post = $this->input->post->getArray();
		$badge->bind($post);

		// Description might contain html codes
		$description = $this->input->get('description', '', 'raw');
		$badge->description = $description;

		if (!$badge->created || $badge->created == '0000-00-00 00:00:00') {
			$badge->created = ED::date()->toSql();
		}

		// Set the badge alias if necessary.
		if ($badge->title != $oldTitle || $oldTitle == '') {
			$badge->alias = ED::getAlias($badge->title);
		}

		// Test for rules here.
		if (!$badge->title || !$badge->description) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_BADGE_SAVE_FAILED'), DISCUSS_QUEUE_ERROR);
			return ED::redirect($redirect . '&layout=form');
		}

		$badge->store();

		$message = $isNew ? JText::_('COM_EASYDISCUSS_BADGE_CREATED') : JText::_('COM_EASYDISCUSS_BADGE_UPDATED');

		// Set the message
		ED::setMessage($message, 'success');

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlogMsg = $isNew ? 'COM_ED_ACTIONLOGS_CREATED_BADGE' : 'COM_ED_ACTIONLOGS_UPDATED_BADGE';

		$actionlog->log($actionlogMsg, 'badges', array(
			'link' => 'index.php?option=com_easydiscuss&view=badges&layout=form&id=' . $badge->id,
			'badgeTitle' => $badge->title
		));

		if ($task == 'save2new') {
			return ED::redirect($redirect . '&layout=form');
		}

		if ($task == 'apply') {
			return ED::redirect($redirect . '&layout=form&id=' . $badge->id);
		}

		return ED::redirect($redirect);
	}

	/**
	 * Mass assign points for users
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function massAssign()
	{
		// Get the file from the request
		$file = $this->input->get('package', '', 'FILES');

		// Get the data from the file.
		$data = ED::parseCSV($file['tmp_name'], false, false);

		if (!$data) {

			$message = JText::_('COM_EASYDISCUSS_BADGES_UPLOAD_CSV_FILE');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			ED::redirect('index.php?option=com_easydiscuss&view=badges=&layout=assign');
			return false;
		}

		// load up the badges library
		$badges = ED::badges();

		// Let's assign the badge now
		foreach ($data as $row) {
			$userId = isset($row[0]) ? trim($row[0]) : false;
			$badgeId = isset($row[1]) ? trim($row[1]) : false;
			$dateAchieved = isset($row[2]) ? trim($row[2]) : ED::date()->toSql();

			$badge = ED::table('badges');
			$badge->load($badgeId);

			// If user id and badge id is empty, skip this.
			if (!$userId || !$badgeId || !$badge->id) {
				continue;
			}

			// Checks whether this user is already achieve this badge. If true, then skip this.
			$badgeUser = ED::table('BadgesUsers');
			if ($badgeUser->loadByUser($userId, $badgeId)) {
				continue;
			}

			// Create the badge & let badge library handle it.
			ED::badges()->create($userId, $badgeId, $dateAchieved);
		}

		$redirect = 'index.php?option=com_easydiscuss&view=badges&layout=assign';
		$message = JText::_('COM_EASYDISCUSS_BADGE_ASSIGNED_SUCESS');

		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);
		ED::redirect($redirect);
	}
}
