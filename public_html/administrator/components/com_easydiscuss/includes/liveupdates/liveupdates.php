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

class EasyDiscussLiveupdates extends EasyDiscuss
{
	/**
	 * Perform the updates for the live update
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function updates($ids, $rExclusion, $cExclusion, $from)
	{
		$updates = [];
		$data = [];
		$my = JFactory::getUser();
		$config = ED::config();
		$sort = $config->get('layout_replies_sorting');

		foreach ($ids as $key => $id) {
			$post = ED::post($id);

			$obj = new stdClass();
			$obj->id = $post->id;
			$obj->isQuestion = false;
			$obj->isReply = false;
			$obj->isAnswer = $post->isAnswer() ? true : false;
			$obj->isModerator = ED::isSiteAdmin() || ED::isModerator() ? true : false;
			$obj->comments = [];

			// Skip it if the post id is invalid, reply has been deleted or the reply is no longer belongs to the discussion
			if (!is_numeric($id) || !$post->id || ($key != 0 && $post->post->parent_id != $ids[0])) {
				continue;
			}

			if (!$post->isPublished() || !$post->canView($my->id, false)) {
				continue;
			}

			$commentsSetting = $post->isQuestion() ? 'main_commentpost' : 'main_comment'; 

			if ($config->get($commentsSetting)) {
				// Check if there are new comments
				$comments = $post->getComments(null, null, false, $cExclusion, $from);
				$obj->hasNewComments = false;

				if ($comments) {
					// Reset it to get latest HTML of it
					$commentsHTML = [];

					foreach ($comments as $comment) {
						// Prevent double comment being added for the comment creator
						if ($comment->user_id == $my->id) {
							continue;
						}

						$commenter = ED::user($comment->user_id);
						$theme = ED::themes();

						$theme->set('comment', $comment);
						$theme->set('isNew', true);
						$content = $theme->output('site/comments/item/default');

						$commentsHTML[] = $content;
						$obj->newComments = $commentsHTML;

						$cExclusion[] = (int) $comment->id;
						$obj->commenterAvatar[$comment->id] = $theme->html('user.avatar', $commenter, ['size' => 'md'], false, true);
						$obj->newCommentMessage[$comment->id] = JText::sprintf('COM_ED_LIVE_UPDATES_DISCUSSION_HAS_NEW_COMMENT', $commenter->getName());
					}

					if (count($commentsHTML) > 0) {
						$obj->hasNewComments = true;
					}
				}
			}

			if ($post->isQuestion()) {
				$obj->isQuestion = true;
				$obj->canComment = false;

				$answer = $post->getAcceptedReply(true);
				$obj->hasAnswer = false;
				$obj->answerMessage = JText::_('COM_ED_LIVE_UPDATES_DISCUSSION_REMOVED_ANSWER_MESSAGE');

				if ($answer && $answer !== true) {
					$obj->hasAnswer = true;
					$obj->answerMessage = JText::_('COM_ED_LIVE_UPDATES_DISCUSSION_HAS_ANSWER_MESSAGE');

					// Retrieve the comments of the answer
					$commentLimit = $config->get('main_comment_pagination') ? $config->get('main_comment_pagination_count') : null;
					$answer->comments = $answer->getComments($commentLimit, null, false);

					$theme = ED::themes();
					$theme->set('post', $answer);
					$theme->set('poll', $answer->getPoll());
					$theme->set('fromAnswer', true);

					$obj->answer = $theme->output('site/post/replies/item');
					$obj->answerId = (int) $answer->id;
				}

				$obj->hasNewReplies = false;
				$obj->repliesSort = $sort;

				$rOptions = array('sort' => $sort, 'useCache' => false, 'excludeIds' => $rExclusion, 'since' => $from);
				$replies = $post->getReplies($rOptions);

				// Check for new replies
				if ($replies) {
					// Reset it to get latest HTML of it
					$repliesHTML = [];
					$obj->newReplyMessage = [];
					$obj->replyerAvatar = [];

					foreach ($replies as $reply) {
						// We also need to check for comments of the new replies as well
						$ids[] = $reply->id;

						// Prevent double reply being added for the reply creator
						if ($reply->user_id == $my->id) {
							continue;
						}

						$replyer = ED::user($reply->user_id);
						$theme = ED::themes();

						$theme->set('post', $reply);
						$theme->set('poll', $reply->getPoll());
						$theme->set('fromAnswer', false);
						$content = $theme->output('site/post/replies/item');

						$repliesHTML[] = $content;
						$obj->replies = $repliesHTML;

						$rExclusion[] = (int) $reply->id;
						$obj->replyerAvatar[$reply->id] = $theme->html('user.avatar', $replyer, ['size' => 'md'], $reply->isAnonymous(), true);
						$obj->newReplyMessage[$reply->id] = JText::sprintf('COM_ED_LIVE_UPDATES_DISCUSSION_HAS_NEW_REPLY', $replyer->getName());
					}

					if (count($repliesHTML) > 0) {
						$obj->hasNewReplies = true;
					}
				}

				$obj->totalReplies = $post->getTotalReplies(false);
			}

			if ($post->isReply()) {
				$obj->isReply = true;
			}

			$data[] = json_encode($obj);
		}

		$updates['data'] = $data;
		$updates['ids'] = $ids;
		$updates['rExclusion'] = $rExclusion;
		$updates['cExclusion'] = $cExclusion;

		return $updates;
	}
}