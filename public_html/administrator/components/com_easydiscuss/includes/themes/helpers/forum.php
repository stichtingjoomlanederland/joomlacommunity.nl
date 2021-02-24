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

class EasyDiscussThemesHelperPost extends EasyDiscuss
{
	/**
	 * Generates the attachments indicator for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function attachments()
	{
		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/attachments');

		return $output;
	}

	/**
	 * Generates the category label
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function category($category)
	{

		$theme = ED::themes();
		$theme->set('category', $category);
		$output = $theme->output('site/helpers/post/category');

		return $output;
	}

	/**
	 * Generates the featured label
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function featured()
	{
		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/featured');

		return $output;
	}

	/**
	 * Generates the hidden indicator for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function hidden()
	{
		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/hidden');

		return $output;
	}

	/**
	 * Generates the locked label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function locked()
	{
		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/locked');

		return $output;
	}

	/**
	 * Generates the new label
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function new()
	{
		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/new');

		return $output;
	}

	/**
	 * Renders the custom post label for the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function postLabel($label)
	{
		$theme = ED::themes();
		$theme->set('label', $label);

		$output = $theme->output('site/helpers/post/post.label');

		return $output;
	}

	/**
	 * Generates the password label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function password()
	{
		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/password');

		return $output;
	}

	/**
	 * Generates the protected label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function protectedPost()
	{
		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/protected');

		return $output;
	}

	/**
	 * Renders the list of participants for the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function participants($participants)
	{
		if (!$participants) {
			return;
		}

		$theme = ED::themes();
		$theme->set('participants', $participants);
		$output = $theme->output('site/helpers/post/participants');

		return $output;
	}

	/**
	 * Renders the list of participants for the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function priority(DiscussPriority $priority)
	{
		$theme = ED::themes();
		$theme->set('priority', $priority);
		$output = $theme->output('site/helpers/post/priority');

		return $output;
	}

	/**
	 * Generates the resolved label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function resolved()
	{
		if (!$this->config->get('main_qna')) {
			return;
		}

		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/resolved');

		return $output;
	}

	/**
	 * Generates the post type label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function type(EasyDiscussPost $post)
	{
		$type = $post->getPostTypeObject();

		if (!$type) {
			return;
		}

		$theme = ED::themes();
		$theme->set('post', $post);
		$theme->set('type', $type);

		$output = $theme->output('site/helpers/post/type');

		return $output;
	}
}
