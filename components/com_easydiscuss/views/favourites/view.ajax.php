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

class EasyDiscussViewFavourites extends EasyDiscussView
{
	/**
	 * Allows caller to mark a post as favourite
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function favourite()
	{
		// Get the post.
		$postId	= $this->input->get('postid', 0, 'int');

        // Load the post object
        $post = ED::post($postId);

        if (!$post->canFav()) {
        	return $this->ajax->reject();
        }

		// Here we need to load the favourite library
		$favourite = ED::favourite();

		// Lets library do the work
		$result = $favourite->favourite($post);


		return $this->ajax->resolve($result);

	}

	/**
	 * Processes a unfavourite request
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function unfavourite()
	{
		// Get the post.
		$postId = $this->input->get('postid', 0, 'int');

        // Load the new post object
        $post = ED::post($postId);

		if (!$post->canFav()) {
			return $this->ajax->reject();
        }

		// Here we need to load the favourite library
		$favourite = ED::favourite();

		// Lets library do the work
		$result = $favourite->unfavourite($post);
		
		// Return the result
		return $this->ajax->resolve($result);
	}

	/**
	 * Processes the popbox request
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function popbox()
	{
		// Get the post.
		$postId = $this->input->get('postid', 0, 'int');

		$user = ED::user();

		// Here we need to load the favourite library
		$result = ED::favourite()->getFavourite($postId, $user->id);

		$theme = ED::themes();
		$theme->set('result', $result);
		$theme->set('action', 'COM_EASYDISCUSS_POPBOX_FAVOURITE');

		$output = $theme->output('site/favourites/popbox');

		return $this->ajax->resolve($output);
	}
}
