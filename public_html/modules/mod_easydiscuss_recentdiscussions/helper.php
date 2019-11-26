<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class modRecentDiscussionsHelper
{
	public static function getData($params)
	{
		$count = (int) $params->get('count', 10);
		$filter = (int) $params->get('filter_option', 0);
		$includeSubcat = (bool) $params->get('include_subcategories', 0);
		$sorting = $params->get('ordering_option', 'latest');

		$catId = $params->get('category', array(), 'array');
		$tagId = intval($params->get('tags', 0));

		$options = array();

		// default sorting will be by latest
		$options['sort'] = $sorting;
		$options['includeChilds'] = false;
		$options['limit'] = $count;
		$options['respectSearch'] = false;

		// Let the model knows this comes from module
		$options['module'] = true;

		$model = ED::model('Posts');

		// unanswered post
		if ($filter == '4') {
			$options['filter'] = 'unanswered';
		}

		// featured posts
		if ($filter == '3') {
			$options['featured'] = true;
		}


		// Filter by tags
		if ($filter == '2' && $tagId) {
			$results = $model->getTaggedPost($tagId, $options['sort'], '', '', $options['limit'], false);

			if (!$results) {
				return;
			}

			return self::format($results);
		}

		// by category id
		if ($filter == '1' && $catId) {
			$options['category'] = $catId;

			if ($includeSubcat) {
				$options['includeChilds'] = true;
			}
		}

		$results = $model->getDiscussions($options);

		if (!$results) {
			return false;
		}

		return self::format($results);
	}

	public static function format(&$results)
	{
		// preload posts
		ED::post($results);

		// preload users
		$ids = array();

		foreach ($results as $row) {
			$ids[] = $row->user_id;
		}

		ED::user($ids);

		$posts = ED::modules()->format($results);

		return $posts;
	}
}