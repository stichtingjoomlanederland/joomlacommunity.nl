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

jimport('joomla.utilities.date');

class EasyDiscussRatings extends EasyDiscuss
{
	/**
	 * Rate a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function html(EasyDiscussPost $post, $disabled = false)
	{
		// Check if ratings has been disabled.
		if (!$this->config->get('main_ratings')) {
			return false;
		}

		// Generate the has for the current user
		$hash = !$this->my->guest ? '' : JFactory::getSession()->getId();

		// Determine if the current user has already voted
		$voted = $post->hasRated($this->my->id);

		$locked = false;

		if ($voted || ($this->my->guest && !$this->config->get('main_ratings_guests') || $disabled)) {
			$locked = true;
		}

		// Get the rating value for the post
		$ratings = $post->getRatings();

		$score = $ratings->ratings;
		$total = $ratings->total;

		$ratingSchemaValue = $score;
		$ratingSchemaTotal = $total;
		$ratingSchemaHasValue = true;
		$ratingSchemaHasTotal = true;

		// set a default rating total value for schema purpose
		if (!$total) {
			$ratingSchemaValue = 1;
			$ratingSchemaTotal = 0;
			$ratingSchemaHasValue = false;
			$ratingSchemaHasTotal = false;			
		}

		$theme = ED::themes();
		$theme->set('voted', $voted);
		$theme->set('post', $post);
		$theme->set('score', $score);
		$theme->set('total', $total);
		$theme->set('locked', $locked);
		$theme->set('ratingSchemaValue', $ratingSchemaValue);
		$theme->set('ratingSchemaTotal', $ratingSchemaTotal);
		$theme->set('ratingSchemaHasValue', $ratingSchemaHasValue);
		$theme->set('ratingSchemaHasTotal', $ratingSchemaHasTotal);

		$output = $theme->output('site/ratings/form');

		return $output;
	}

	public function rate($postId, $score)
	{
		// Get the user's session
		$session = JFactory::getSession();

		// Load up the rating table
		$rating	= ED::table('Ratings');

		// Set the ratings property
		$rating->created_by = $this->my->id;
		$rating->type = 'question';
		$rating->uid = $postId;
		$rating->ip = @$_SERVER['REMOTE_ADDR'];
		$rating->value = $score;
		$rating->sessionid = $session->getId();
		$rating->created = ED::date()->toSql();
		$rating->published = true;
		$rating->store();

		// Get the updated ratings value
		$post = ED::post($postId);

		$results = $post->getRatings();

		$data = new stdClass();
		$data->ratings = $results->ratings;
		$data->total = $results->total;
		$data->message = JText::_('COM_EASYDISCUSS_RATINGS_THANK_YOU');

		return $data;
	}
}
