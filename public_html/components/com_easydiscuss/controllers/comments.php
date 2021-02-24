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

class EasydiscussControllerComments extends EasyDiscussController
{
	/**
	 * Converts a comment into a discussion reply
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function convert()
	{
		ED::checkToken();

		// Get the comment and post id from the request.
		$commentId = $this->input->get('commentId', 0, 'int');
		$postId = $this->input->get('postId', 0, 'int');

		$comment = ED::comment($commentId);

		if (!$comment->canConvert()) {
			die();
		}

		// Throws error if the comment id is not provided.
		if (!$commentId || !$comment->id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_COMMENTS_INVALID_COMMENT_ID_PROVIDED'), 'error');
			return;
		}

		// Load the post library
		$post = ED::post($postId);

		// Throws error if the post id is not provided.
		if (!$postId || !$post->id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_COMMENTS_INVALID_POST_ID_PROVIDED'), 'error');
			return;
		}

		// Get redirection link so that we can redirect the user back
		$permalink = $post->getPermalink(false, false);

		// If this post is not a question, we'll need to get the parent id.
		if (!$post->isQuestion()) {
			$parent = $post->getParent();

			// Re-assign $post to be the parent.
			$post = ED::post($parent->id);

			$permalink = $post->getPermalink(false, false);
		}

		$content = $comment->comment;

		$editor = $this->config->get('layout_editor');

		if ($editor != 'bbcode') {
			$content = nl2br($content);
		}

		// For contents, we need to get the raw data.
		$data['content'] = $content;
		$data['parent_id'] = $post->id;
		$data['user_id'] = $comment->user_id;
		$data['latitude'] = '';
		$data['longitude'] = '';

		// Load the post library
		$post = ED::post();
		$post->bind($data);

		// Try to save the post now
		$state = $post->save();

		// Throws error if the store process hits error
		if (!$state) {
			ED::setMessage('COM_EASYDISCUSS_COMMENTS_ERROR_SAVING_REPLY', 'error');

			return ED::redirect($permalink);
		}

		// Once the reply is successfully stored, delete the particular comment.
		$comment->delete();

		ED::setMessage('COM_EASYDISCUSS_COMMENTS_SUCCESS_CONVERTED_COMMENT_TO_REPLY', 'success');

		return ED::redirect($permalink);
	}
}
