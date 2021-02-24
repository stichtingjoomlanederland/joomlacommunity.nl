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

class EasyDiscussViewProfileAbstract extends EasyDiscussView
{
	private $pagination = null;

	/**
	 * Internal method to get posts data
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	protected function getProfilePosts($filter, $user)
	{
		$model = ED::model('Posts');

		if ($filter == 'replies') {
			$posts = $model->getRepliesFromUser($user->id, 'lastreplied');
			$this->pagination = $model->getPagination();
		}

		if ($filter == 'favourites') {
			$options = [
				'userId' => $user->id,
				'filter' => 'favourites'
			];

			$posts = $model->getDiscussions($options);
			$this->pagination = $model->getPagination();
		}

		if ($filter == 'assigned') {
			$assignedModel = ED::model('Assigned');

			$posts = $assignedModel->getPosts($user->id);
			$this->pagination = $assignedModel->getPagination();
		}

		if ($filter == 'pending' && ((ED::isModerator() || ($this->my->id == $user->id)))) {
			$options = array(
				'filter' => $filter, 
				'userId' => $user->id, 
				'includeAnonymous' => true, 
				'includeCluster' => false,
				'private' => true,
				'published' => DISCUSS_ID_PENDING
			);

			$posts = $model->getDiscussions($options);
			$this->pagination = $model->getPagination();
		}
		
		// Default will always be active posts
		if ($filter == 'posts') {
			$options = [
				'filter' => 'all',
				'userId' => $user->id,
				'includeAnonymous' => false,
				'private' => false,
				'includeCluster' => false
			];

			// If user is trying to view their own posts, we display the anonymous posts
			if ($this->my->id == $user->id) {
				$options['includeAnonymous'] = true;
				$options['private'] = true;
				$options['includeCluster'] = true;
			}

			$posts = $model->getDiscussions($options);

			$this->pagination = $model->getPagination();
		}

		if ($posts) {
			ED::post($posts);
			$posts = ED::formatPost($posts);
		}

		return $posts;
	}

	protected function getProfilePagination()
	{
		return $this->pagination;
	}
}
