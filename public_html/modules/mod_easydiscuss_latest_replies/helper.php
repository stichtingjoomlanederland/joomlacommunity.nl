<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

class modEasydiscussLatestRepliesHelper
{
	public static function getData($params)
	{
		$db = ED::db();
		$count = (INT)trim($params->get('count', 0));

		$model = ED::model( 'Posts' );
		$result	= $model->getRecentReplies($count);

		if (!$result) {
			return $result;
		}

		//preload users
		$users = array();
		$parents = array();

		foreach ($result as $item) {
			$users[] = $item->user_id;
			$parents[] = $item->parent_id;
		}

		// preload users
		ED::user($users);

		// preload posts
		ED::post($parents);

		$replies = array();

		$result = ED::modules()->format($result);

		foreach ($result as $item) {
			$item->profile = ED::user($item->user_id);

			$item->content = $item->getContent();
			$item->content = ($params->get('maxlength', 0)) ? JString::substr(strip_tags($item->content), 0, $params->get('maxlength')) . '...' : $postcontent;

			$item->title = ED::badwords()->filter($item->title);

			// load the parent
			$item->question = ED::post($item->parent_id);

			$replies[] = $item;
		}

		return $replies;
	}
}
