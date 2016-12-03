<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewIndex extends EasyDiscussView
{
	/**
	 * JSON API for listing recent items
	 *
	 * @json	1.0
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function display($tpl = null)
	{
		$filter = $this->input->get('filter', '', 'string');

		if ($filter) {
			$activeFilter = $filter;
		}

		// Determines if we should be sorting the view
		$sort = $this->input->get('sort', '', 'string');

		// Get the pagination limit
		$limit = ED::getListLimit();

		// Get list of categories on the site.
		$catModel = ED::model('Categories');

		// Pagination is by default disabled.
		$pagination = false;

		// Get the model.
		$postModel = ED::model('Posts');

		// Get featured posts from this particular category.
		$featured = array();

		if ($this->config->get('layout_featuredpost_frontpage')) {

			$options 	= array(
									'pagination' => false,
									'sort' => 'latest',
									'filter' => $this->config->get('layout_frontpage_sorting'),
									'limit' => $this->config->get( 'layout_featuredpost_limit' , $limit ),
									'featured' => true
							);
			$featured	= $postModel->getDiscussions( $options );
			if (is_null($featured)) {
				$featured = array();
			}
		}

		// Get normal discussion posts.
		$options 	= array(
						'sort'		=> $sort,
						'filter'	=> $filter,
						'limit'		=> $limit,
						'featured'	=> false
					);

		$posts = $postModel->getDiscussions($options);

		if (is_null($posts)) {
			$posts = array();
		}

		$authorIds = array();
		$topicIds = array();
		$tmpPostsArr = array_merge($featured, $posts);

		if ($tmpPostsArr) {

			//preload posts
			ED::post($tmpPostsArr);

			foreach ($tmpPostsArr as $tmpArr) {
				$authorIds[] = $tmpArr->user_id;
				$topicIds[] = $tmpArr->id;
			}
		}

		$pagination = $postModel->getPagination();


		$model = ED::model('Posts');

		// Reduce SQL queries by pre-loading all author object.
		$lastReplyUser = array();
		$authorIds = array_merge($lastReplyUser, $authorIds);
		$authorIds = array_unique($authorIds);

		//preload users.
		ED::user($authorIds);

		// Format featured entries.
		$featured = ED::formatPost($featured);

		if ($featured) {

		}

		// Format normal entries
		$tmpPosts = ED::formatPost($posts);
		$posts = array();

		if ($tmpPosts) {
			foreach ($tmpPosts as $post) {
				$posts[] = $post->toData();
			}
		}

		$this->set('posts', $posts);

		return parent::display();
	}
}
