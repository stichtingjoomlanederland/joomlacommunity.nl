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

class EasyDiscussViewPopbox extends EasyDiscussView
{
	/**
	 * Renders a popbox for category
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function category()
	{
		$id = $this->input->get('id', 0, 'int');

		// guest should not allowed.
		if (!$id) {
			return $this->ajax->fail(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'));
		}

		$category = ED::category($id);

		// Respecting ACL and category permissions.
		$ask = ED::post()->canPostNewDiscussion($category->id);

		$theme = ED::themes();
		$theme->set('category', $category);
		$theme->set('ask', $ask);
		$contents = $theme->output('site/popbox/category');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Renders a popbox for a badge
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function badge()
	{
		$id = $this->input->get('id', 0, 'int');

		// guest should not allowed.
		if (!$id) {
			return $this->ajax->fail(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'));
		}

		$badge = ED::table('Badges');
		$badge->load($id);


		$theme = ED::themes();
		$theme->set('badge', $badge);
		$contents = $theme->output('site/popbox/badge');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Renders the users profile popbox
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function user()
	{
		$id = $this->input->get('id', 0, 'int');

		// guest should not allowed.
		if (!$id) {
			return $this->ajax->fail(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'));
		}

		$user = ED::user($id);
		$badges = $user->getBadges();

		$theme = ED::themes();
		$theme->set('user', $user);
		$theme->set('badges', $badges);
		$contents = $theme->output('site/popbox/user');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Renders the voters popbox
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function voters()
	{
		$postId = $this->input->get('id', 0, 'int');

		if (!$postId) {
			return $this->ajax->fail(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'));
		}

		if (!$this->config->get('main_allowguestview_whovoted') && !$this->my->id) {
			return $this->ajax->fail(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'));
		}

		$voteModel = ED::model('Votes');
		$voters = $voteModel->getVoters($postId);
		$guests = 0;
		$users = array();

		if ($voters) {
			$ids = array();

			foreach ($voters as $voter) {
				if (!$voter->user_id) {
					$guests++;
				} else {
					$ids[] = $voter->user_id;
				}
			}

			if ($ids) {
				// preload users
				ED::user($ids);

				foreach ($ids as $id) {
					$users[] = ED::user($id);
				}
			}
		}

		$theme = ED::themes();
		$theme->set('users', $users);
		$theme->set('guests', $guests);
		$theme->set('type', 'voters');
		$theme->set('title', JText::_('COM_EASYDISCUSS_VIEWING_VOTERS_TITLE'));
		$theme->set('emptyMessage', JText::_('COM_EASYDISCUSS_NO_VOTERS_YET'));
		$contents = $theme->output('site/popbox/users');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Renders the likers popbox
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function likers()
	{
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->fail(JText::_('COM_EASYDISCUSS_INVALID_ID_PROVIDED'));
		}

		$post = ED::post($id);

		// If the post cannot be liked, we should not display anything
		if (!$post->canLike()) {
			return $this->ajax->fail(JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_PERFORM_THE_REQUESTED_ACTION'));
		}

		$likers = ED::likes()->getLikes($post->id, $this->my->id);

		$theme = ED::themes();
		$theme->set('users', $likers);
		$theme->set('type', 'likers');
		$theme->set('title', JText::_('COM_EASYDISCUSS_POPBOX_LIKES'));
		$theme->set('emptyMessage', JText::_('COM_EASYDISCUSS_NO_LIKES_YET'));

		$output = $theme->output('site/popbox/users');

		return $this->ajax->resolve($output);
	}

	/**
	 * Display the popbox for who has favourited
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function favourite()
	{
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->fail(JText::_('COM_EASYDISCUSS_INVALID_ID_PROVIDED'));
		}

		$post = ED::post($id);

		// If the post cannot be favourited, we should not display anything
		if (!$post->canFav()) {
			return $this->ajax->fail(JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_PERFORM_THE_REQUESTED_ACTION'));
		}

		$users = ED::favourite()->getFavourite($post->id, $this->my->id);

		$theme = ED::themes();
		$theme->set('users', $users);
		$theme->set('type', 'favourited');
		$theme->set('title', JText::_('COM_EASYDISCUSS_POPBOX_FAVOURITE'));
		$theme->set('emptyMessage', JText::_('COM_ED_NO_FAVOURITE_YET'));

		$output = $theme->output('site/popbox/users');

		return $this->ajax->resolve($output);
	}
}
