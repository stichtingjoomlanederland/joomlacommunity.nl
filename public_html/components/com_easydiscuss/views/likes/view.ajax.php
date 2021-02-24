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

class EasyDiscussViewLikes extends EasyDiscussView
{
	/**
	 * Processes a like request
	 *
	 * @since	4.0
	 * @access	public
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

		$tooltip = JText::_('COM_ED_UNLIKE_TOOLTIP_TITLE');

		// Return the result
		return $this->ajax->resolve($tooltip);
	}

	/**
	 * Processes an unlike request
	 *
	 * @since	4.0
	 * @access	public
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

		$tooltip = JText::_('COM_ED_LIKE_TOOLTIP_TITLE');

		// Return the result
		return $this->ajax->resolve($tooltip);
	}
}
