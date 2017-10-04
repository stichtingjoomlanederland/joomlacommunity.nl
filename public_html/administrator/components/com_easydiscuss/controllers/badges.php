<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class EasyDiscussControllerBadges extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		// Need to explicitly define this in Joomla 3.0
		$this->registerTask('unpublish', 'unpublish');
		$this->registerTask('publish', 'unpublish');
		$this->registerTask('savePublishNew', 'save');
		$this->registerTask('assign', 'assign');
	}

	public function assign()
	{
		$this->app->redirect('index.php?option=com_easydiscuss&view=badges&layout=assign');
	}

	public function edit()
	{
		JRequest::setVar('view', 'badge');
		JRequest::setVar('id', JRequest::getInt('id', '', 'REQUEST'));

		parent::display();
	}

	public function add()
	{
		$this->app->redirect('index.php?option=com_easydiscuss&view=badges&layout=form');
	}

	public function cancel()
	{
		$this->app->redirect('index.php?option=com_easydiscuss&view=badges');

		return;
	}

	public function remove()
	{
		JRequest::checkToken('request') or jexit('Invalid Token');
		$ids = $this->input->get('cid');

		foreach ($ids as $id) {
			$badge = ED::table('Badges');
			$badge->load($id);
			$badge->delete();
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_BADGES_DELETED'), DISCUSS_QUEUE_SUCCESS);
		$this->app->redirect('index.php?option=com_easydiscuss&view=badges');
	}

	public function unpublish()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$badge = ED::table('Badges');
		$ids = $this->input->get('cid', '', 'array');
		$state = $this->input->get('task') == 'publish' ? 1 : 0;

		foreach ($ids as $id) {
			$id = (int) $id;

			$badge->load($id);

			$badge->set('published', $state);
			$badge->store();
		}

		$message = $state ? JText::_('COM_EASYDISCUSS_BADGES_PUBLISHED') : JText::_('COM_EASYDISCUSS_BADGES_UNPUBLISHED');

		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		$this->app->redirect('index.php?option=com_easydiscuss&view=badges');
	}

	/**
	 * Method to save a badge and stay on same page
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function apply()
	{
		$id = $this->input->get('id');
		$isNew = false;

		$redirect = 'index.php?option=com_easydiscuss&view=badges&layout=form&id=' . $id;

		if (!$id) {
			$isNew = true;
			$redirect = 'index.php?option=com_easydiscuss&view=badges&layout=form';
		}

		return $this->save($redirect, $isNew);
	}

	/**
	 * Method to save a badge and open a new form afterwards
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save2new()
	{
		$redirect = 'index.php?option=com_easydiscuss&view=badges&layout=form';
		return $this->save($redirect);
	}

	/**
	 * Method to save a badge
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function save($redirect = 'index.php?option=com_easydiscuss&view=badges', $isNew = false)
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$badge = ED::table('Badges');
		$id = $this->input->get('id');

		// Load the badge.
		$badge->load($id);

		$oldTitle = $badge->title;

		$post = JRequest::get('POST');
		$badge->bind($post);

		// Description might contain html codes
		$description = JRequest::getVar( 'description' , '' , 'post' , 'string' , JREQUEST_ALLOWRAW );
		$badge->description = $description;

		if (!$badge->created || $badge->created == '0000-00-00 00:00:00') {
			$badge->created = ED::date()->toSql();
		}

		// Set the badge alias if necessary.
		if ($badge->title != $oldTitle || $oldTitle == '') {
			$badge->alias = ED::getAlias($badge->title);
		}

		// Get the current task
		$task = $this->getTask();

		// Test for rules here.
		if (!$badge->title || !$badge->description) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_BADGE_SAVE_FAILED'), DISCUSS_QUEUE_ERROR);

			JRequest::setVar('view', 'badge');

			return $this->app->redirect($redirect);
		}

		$badge->store();

		$message = !empty($id) ? JText::_('COM_EASYDISCUSS_BADGE_UPDATED') : JText::_('COM_EASYDISCUSS_BADGE_CREATED');

		if ($isNew && $badge->id) {
			$redirect = $redirect . '&id=' . $badge->id;
		}

		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);
		$this->app->redirect($redirect);
	}

	/**
	 * Mass assign points for users
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function massAssign()
	{
		// Get the file from the request
		$file = JRequest::getVar('package', '', 'FILES');

		// Get the data from the file.
		$data = ED::parseCSV($file['tmp_name'], false, false);

		if (!$data) {

			$message = JText::_('COM_EASYDISCUSS_BADGES_UPLOAD_CSV_FILE');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			$this->app->redirect('index.php?option=com_easydiscuss&view=badges=&layout=assign');
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
		$this->app->redirect($redirect);
	}
}
