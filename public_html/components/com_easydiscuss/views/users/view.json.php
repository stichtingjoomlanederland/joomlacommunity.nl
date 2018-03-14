<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewUsers extends EasyDiscussView
{
	public function isFeatureAvailable()
	{
		if (!$this->config->get('main_user_listings')) {
			return false;
		}

		return true;
	}

	/**
	 * Renders users listing
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tmpl = null)
	{
		// Get the list of users
		$model = ED::model('Users');
		$users = $model->getData();
		$pagination	= $model->getPagination();

		// Format the result
		$users = ED::formatUsers($users);

		// Other options
		$sort = $this->input->get('sort', 'name', 'cmd');
		$filter = $this->input->get('filter', 'allposts', 'string');


		// @TODO: We neeed to switch this to mysql expression
		// Really really bad implementation!
		// Get a list of users to be excluded.
		$excluded = $this->config->get('main_exclude_members');

		if (!empty($excluded)) {
			// Remove white space
			$uids = str_replace(' ', '', $excluded);
			$excludeId = explode(',', $uids);

			$temp = array();

			foreach ($users as $user) {
				if( !in_array($user->id, $excludeId)) {
					$temp[] = $user;
				}
			}
			$users = $temp;
		}

		$data = array();

		if ($users) {
			foreach ($users as $user) {
				$obj = new stdClass();
				$obj->name = $user->getName();
				$obj->avatar = $user->getAvatar();
				$obj->permalink = $user->getPermalink();
				$obj->description = $user->getDescription();
				$obj->lastonline = $user->getLastOnline();
				$obj->posts = $user->getTotalQuestions();
				$obj->replies = $user->getTotalReplies();
				$obj->badges = $user->getTotalBadges();
				
				$data[] = $obj;
			}
		}

		$this->set('users', $data);

		// // dump($data);
		// dump($users);
		parent::display();
	}
}
