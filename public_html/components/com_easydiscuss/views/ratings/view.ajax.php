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
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewRatings extends EasyDiscussView
{
	public function submit()
	{
		$score = $this->input->get('score', 0, 'int');
		$postId = $this->input->get('postId', 0, 'int');

		$post = ED::post($postId);

		if (!$postId || !$post->id) {
			return $this->ajax->reject();
		}

		// Check if the current user already rated this item.
		$rated = $post->hasRated();

		if ($rated) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_RATINGS_ALREADY_RATED_BEFORE'));
		}

		$ratings = ED::ratings();

		$results = $ratings->rate($postId, $score);

		return $this->ajax->resolve($results->ratings, $results->total, $results->message);
	}
}