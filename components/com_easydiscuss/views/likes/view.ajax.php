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

class EasyDiscussViewLikes extends EasyDiscussView
{
	/**
	 * Processes a like request
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function like()
	{
		// Get the post.
		$postId = $this->input->get('postid', 0, 'int');

        // Load the new post object
        $post = ED::post($postId);

		// Determine if the likes are enabled or not.
		if (!$post->canLike()) {
			return $this->ajax->reject();
		}

		// Here we need to load the likes library
		$likes = ED::likes();

		// Let the library do the work.
		$result = $likes->like($post);
		
		// Return the result
		return $this->ajax->resolve($result);
	}

	/**
	 * Processes an unlike request
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function unlike()
	{
		// Get the post.
		$postId = $this->input->get('postid', 0, 'int');

        // Load the new post object
        $post = ED::post($postId);

		// Determine if the likes are enabled or not.
		if (!$post->canLike()) {
			return $this->ajax->reject();
		}

		// Here we need to load the likes library
		$likes = ED::likes();

		// Let the library do the work.
		$result = $likes->unlike($post);
		
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

		// Here we need to load the likes library
		$result = ED::likes()->getLikes($postId, $user->id);

		$theme = ED::themes();
		$theme->set('result', $result);
		$theme->set('action', 'COM_EASYDISCUSS_POPBOX_LIKES');

		$output = $theme->output('site/likes/popbox');

		return $this->ajax->resolve($output);
	}
}
