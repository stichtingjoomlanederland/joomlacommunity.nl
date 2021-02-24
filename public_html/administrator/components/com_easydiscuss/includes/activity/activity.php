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

require_once(__DIR__ . '/adapters/abstract.php');

class EasyDiscussActivity
{

	/**
	 * Determines if activity feature is enabled
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isEnabled()
	{
		$config = ED::config();
		if ($config->get('main_post_activity') == 'disable') {
			return false;
		}

		return true;
	}

	/**
	 * Determines if activity feature is enabled
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function canView($post)
	{
		$config = ED::config();
		if ($config->get('main_post_activity') == 'everyone') {
			return true;
		}

		if ($config->get('main_post_activity') == 'moderator' && ED::isModerator($post->category_id)) {
			return true;
		}

		return false;
	}

	/**
	 * Get the data template
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getTemplate()
	{
		$tmpl = new EasyDiscussActivityTemplate();
		return $tmpl;
	}

	/**
	 * Store the user action into the log table
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function log(EasyDiscussActivityTemplate $data)
	{
		if (!$this->isEnabled()) {
			return true;
		}

		if (!$data->validate()) {
			return false;
		}

		$tbl = ED::table('activity');
		$tbl->bind($data);
		$state = $tbl->store();

		if (!$state) {
			return false;
		}

		return $tbl->id;
	}

	/**
	 * Store the user action into the log table
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function fetch($post, $replies = array(), $options = array())
	{
		// check if current user can view or not.
		if (!$this->isEnabled() || !$this->canView($post)) {
			return $replies;
		}

		$sort = ED::normalize($options, 'sort', ED::getDefaultRepliesSorting());
		$limitstart = ED::normalize($options, 'limitstart', 0);
		$isLastPage = ED::normalize($options, 'isLastPage', false);
		$limit = ED::normalize($options, 'limit', 0);
		$totalReplies = ED::normalize($options, 'totalReplies', 0);
		$nextReplyItem = ED::normalize($options, 'nextReplyItem', false);
		$repliesPagination = ED::normalize($options, 'repliesPagination', false);

		$start = '';
		$end = '';

		$rCount = count($replies);

		if ($repliesPagination) {
			if ($limitstart != 0) {
				$start = $replies[0]->created;
				$end = $rCount > 1 ? $replies[$rCount-1]->created : '';

				if ($nextReplyItem) {
					$end = $nextReplyItem->created;
				}

				// this mean we are not at the first pagination of replies.
				if ($sort != 'oldest') {
					$start = $replies[$rCount-1]->created;
					$end = $rCount > 1 ? $replies[0]->created : '';
				}

				// this mean the replies at last page.
				if (($limitstart + $limit) >= $totalReplies) {

					// we should just get all the activities since the first reply on the last page.
					$end = $start;

					if ($sort != 'oldest') {
						$start = $replies[0]->created;
						$end = '';
					}
				}

			} else if ($limitstart == 0 || $isLastPage) {
				$end = $rCount > 0 ? $replies[$rCount-1]->created : '';

				if ($nextReplyItem) {
					$end = $nextReplyItem->created;
				}

				if ($sort != 'oldest') {
					// $end = $replies[0]->created;
					$start = $end;
				}

				if ($totalReplies <= $limit) {
					// we should just show all activities since this post has very little replies.
					$start = '';
					$end = '';
				}
			}
		}

		$model = ED::model('activity');

		$aOptions = array('start' => $start, 'end' => $end, 'sort' => $sort);
		$activities = $model->getLogs('post', $post->id, $aOptions);

		$data = array();
		if ($replies) {
			foreach ($replies as $item) {
				$data[$item->created] = $item;
			}
		}

		if ($activities) {
			$data = array_merge($activities, $data);
		}

		if ($data) {
			if ($sort == 'oldest') {
				ksort($data);
			} else {
				krsort($data);
			}
		}

		return $data;
	}

	/**
	 * method the return the adapter used on the activity log based on the action
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getAdapter($adapter)
	{
		static $adapters = [];
		
		$adapter = strtolower($adapter);

		if (!isset($adapters[$adapter])) {
			$obj = false;

			$file = __DIR__ . '/adapters/' . $adapter . '.php';
			if (JFile::exists($file)) {
				include_once($file);
			}

			$title = str_replace('.', '', $adapter);
			$className = 'EasyDiscussActivity' . ucfirst($title);

			if (class_exists($className)) {
				$obj = new $className();
			}

			$adapters[$adapter] = $obj;
		}

		return $adapters[$adapter];
	}

	/**
	 * method the translate the activity log to a readable content
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function translate($data)
	{
		$content = '';

		// Check if the label is turned off, we shouldn't translate it.
		if ($data->action == 'post.label' && !ED::config()->get('main_labels')) {
			return '';
		}

		$adapter = $this->getAdapter($data->action);
		if ($adapter !== false) {
			$content = $adapter->translate($data);

			if ($content === false) {
				return '';
			}
		}

		return $content;
	}

	/**
	 * method the return the supported actions data
	 *
	 * @since	5.0
	 * @access	public
	 */
	private function getActionData($type) 
	{
		$actions = array(
			'post' => array(
				'post.title' => 'fa-pen',
				'post.category' => 'fa-folder',
				'post.label' => 'fa-tag',
				'post.resolved' => 'fa-check',
				'post.unresolved' => 'fa-undo',
				'post.moderator' => 'fa-user-check',
				'post.locked' => 'fa-lock',
				'post.unlocked' => 'fa-unlock',
				'post.featured' => 'fa-fire',
				'post.unfeatured' => 'fa-fire',
				'post.answer.accept' => 'fa-comment',
				'post.answer.revoke' => 'fa-comment-slash',
				'post.email.parser' => 'fa-envelope'
			)
		);

		$data = isset($actions[$type]) ? $actions[$type] : false;
		return $data;
	}

	/**
	 * method the return the supported actions
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getActions($type = 'post')
	{
		$data = $this->getActionData($type);

		if ($data) {
			// get the keys only.
			$actions = array_keys($data);
			return $actions;
		}

		return false;
	}

	public function getActionIcon($type, $command)
	{
		$data = $this->getActionData($type);

		if ($data && isset($data[$command]) && $data[$command]) {
			return $data[$command];
		}

		return '';
	}

}


class EasyDiscussActivityTemplate
{
	public $utype = null;
	public $uid = null;
	public $user_id = null;
	public $action = null;
	public $old = null;
	public $new = null;
	public $created = null;

	/**
	 * method the clear the assigned value
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function clear()
	{
		$this->utype = null;
		$this->uid = null;
		$this->user_id = null;
		$this->action = null;
		$this->old = null;
		$this->new = null;
		$this->created = null;
	}

	/**
	 * method the validate the require data
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function validate()
	{
		$lib = ED::activity();
		$supported = $lib->getActions($this->utype);

		if (!$this->utype || !$this->uid) {
			return false;
		}

		if (!$this->action) {
			return false;
		}

		if (!in_array($this->action, $supported)) {
			return false;
		}

		if (!$this->old && !$this->new) {
			return false;
		}

		return true;
	}

	/**
	 * set action rule
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function setAction($rule)
	{
		$this->action = $rule;
	}

	/**
	 * set user id who perform this action
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function setActor($userId)
	{
		$this->user_id = $userId;
	}

	/**
	 * set type and id associated with the type
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function setType($type, $uid)
	{
		$this->utype = $type;
		$this->uid = $uid;
	}

	/**
	 * set the log contents
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function setContent($old, $new)
	{
		$this->old = $old;
		$this->new = $new;
	}
}
