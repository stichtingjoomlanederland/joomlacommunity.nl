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

class EasyDiscussViewFavourites extends EasyDiscussView
{
	/**
	 * Allows caller to mark a post as favourite
	 *
	 * @since	4.0
	 * @access	public
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

		$tooltip = JText::_('COM_ED_UNFAVOURITE_TOOLTIP_TITLE');

		return $this->ajax->resolve($tooltip);
	}

	/**
	 * Processes a unfavourite request
	 *
	 * @since	4.0
	 * @access	public
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

		$tooltip = JText::_('COM_ED_FAVOURITE_TOOLTIP_TITLE');

		// Return the result
		return $this->ajax->resolve($tooltip);
	}
}
