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

class EasyDiscussViewComment extends EasyDiscussView
{
	/**
	 * Responsible to process a comment for saving.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function save()
	{
		ED::checkToken();

		$id = $this->input->get('id','','int');
		$acceptedTerms = $this->input->get('tncCheckbox','');

		// Check the terms and condirion if it is enabled
		if ($this->config->get('main_tnc_comment') && $acceptedTerms == 'false') {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_TERMS_PLEASE_ACCEPT'));
		}

		// Load the post item.
		$post = ED::post($id);

		// Check if the user really can post comment for this post
		if (!$post->canComment()) {
			return $this->ajax->reject($post->getError());
		}

		$profile = ED::profile();

		$data = array(
			'user_id' => $this->my->id,
			'name' => $profile->getName(),
			'email' => $this->my->email,
			'comment' => $this->input->get('comments','', 'raw'),
			'post_id' => $id,
			'ip' => @$_SERVER['REMOTE_ADDR'],
			'title' => ''
		);

		$comment = ED::comment();
		$comment->bind($data, true);

		$valid = $comment->validate();

		if (!$valid) {
			return $this->ajax->reject(JText::_($comment->getError()));
		}

		$state = $comment->save();

		if (!$state) {
			return $this->ajax->reject($comment->getError());
		}

		// Get the result of the posted comment.
		$theme = ED::themes();
		$theme->set('comment', $comment);
		$theme->set('isNew', true);
		$contents = $theme->output('site/comments/item/default');

		return $this->ajax->resolve($contents, $comment->id);
	}

	/**
	 * Update the comment.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function update()
	{
		ED::checkToken();

		$id = $this->input->get('id', 0, 'int');

		$comment = ED::comment($id);
		$post = $comment->getPost();

		// Check if the user really can post comment for this post
		if (!$post->canComment()) {
			return $this->ajax->reject($post->getError());
		}

		// Check if the user truly can edit the comment
		if (!$comment->canEdit()) {
			return $this->ajax->reject($comment->getError());
		}

		// Get the updated content
		$content = $this->input->get('comment','', 'raw');

		$content = htmlentities($content);

		// Replace a url to link
		$content = ED::string()->url2link($content);

		$comment->comment = $content;

		if (!$comment->save()) {
			return $this->ajax->reject($comment->getError());
		}

		$message = $comment->getMessage();

		return $this->ajax->resolve($message);
	}

	/**
	 * Displays a confirmation dialog to move a comment to a reply.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function confirmConvert()
	{
		$commentId = $this->input->get('commentId', 0, 'int');
		$postId = $this->input->get('postId', 0, 'int');

		// Test if a valid post id is provided.
		if (!$commentId || !$postId) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_COMMENTS_INVALID_POST_ID'));
		}

		$theme = ED::themes();
		$theme->set('commentId', $commentId);
		$theme->set('postId', $postId);
		$contents = $theme->output('site/comments/dialogs/convert.confirm');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays a confirmation dialog to delete a comment.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function confirmDelete()
	{
		$id = $this->input->get('id', 0, 'int');

		// Test if a valid post id is provided.
		if (!$id) {
			$this->ajax->reject( JText::_('COM_EASYDISCUSS_COMMENTS_INVALID_POST_ID'));
			return $this->ajax->send();
		}

		$theme = ED::themes();
		$theme->set('id', $id);
		$contents = $theme->output('site/comments/dialogs/delete.confirmation');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Responsible to delete a comment.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function delete()
	{
		$id = $this->input->get('id', 0, 'int');

		$comment = ED::comment($id);

		if (!$comment->canDelete()) {
			return $this->ajax->reject('COM_EASYDISCUSS_COMMENTS_NOT_ALLOWED');
		}

		if (!$comment->delete()) {
			return $this->ajax->reject($comment->getError());
		}

		// Check if there any comment left on the post.
		$post = $comment->getPost();
		$comments = $post->getComments();

		return $this->ajax->resolve($comments);
	}

	/**
	 * Shows the terms and condition dialog window.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function showTnc()
	{
		$theme = ED::themes();
		$contents = $theme->output('site/comments/dialogs/comment.term');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Retrieve the comment form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getForm()
	{
		$id = $this->input->get('id', 0, 'int');
		$isEdit = $this->input->get('isEdit', false, 'bool');

		$comment = ED::comment($id);

		if (!$comment->canEdit()) {
			die();
		}

		$post = ED::post($comment->post_id);

		$theme = ED::themes();
		$theme->set('post', $post);
		$theme->set('comment', $comment);
		$theme->set('isEdit', $isEdit);

		$form = $theme->output('site/comments/form/default');

		return $this->ajax->resolve($form);
	}
}
