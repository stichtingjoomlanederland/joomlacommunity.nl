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

class EasyDiscussViewAssignment extends EasyDiscussView
{
	/**
	 * Displays a list of moderators on the site.
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function getModerators()
	{
		if (!ED::isSiteAdmin() && !ED::isModerator()) {
			return;
		}

		$categoryId = $this->input->get('category_id', 0, 'int');	
		$moderators = ED::moderator()->getModeratorsDropdown($categoryId);

		$theme = ED::themes();
		$theme->set('moderators', $moderators);
		$contents = $theme->output('site/helpers/assignment/list');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Allows caller to assign a moderator to a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function assign()
	{
		ED::checkToken();

		$postId = $this->input->get('postId', 0, 'int');
		$moderatorId = $this->input->get('moderatorId', 0, 'int');
		$assigner = JFactory::getUser();

		// Load the new post object
		$post = ED::post($postId);

		if (!$postId || !$post->id) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_ASSIGN_MODERATORS_SHOW_UNABLE_LOAD_POST_ID'));
		}

		$category = ED::category($post->category_id);
		$access = $post->getAccess($category);
		$isClear = false;

		if (!$access->canAssign()) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_ASSIGN_MODERATORS_SHOW_PERMISSION_DENIED'));
		}

		$assignment = ED::table('PostAssignment');
		$assignment->load($post->id);

		$isNew = $assignment->id ? false : true;

		$moderator = null;

		$oldAssignee = !$isNew ? $assignment->assignee_id : 0;
		$newAssignee = $moderatorId;
		$addLog = false;

		// Removing a moderator
		if ($moderatorId === 0 && !$isNew) {
			$assignment->delete();
			$addLog = true;
		} 

		if ($moderatorId) {
			$moderator = ED::user($moderatorId);
			$sendNotti = false;
			$state = true;

			if ($isNew) {
				// new
				$sendNotti = true;
				$assignment->post_id = $postId;
				$assignment->assignee_id = (int) $moderatorId;
				$assignment->assigner_id = (int) $assigner->id;
			} 

			if (!$isNew) {
				// updates
				if ($assignment->assignee_id != $moderatorId) {
					$sendNotti = true;

					$assignment->assignee_id = (int) $moderatorId;
					$assignment->assigner_id = (int) $assigner->id;
				}
			}

			// Notification should be sent to actor for whatever reason
			if ($assigner->id == $moderatorId) {
				$sendNotti = false;
			}

			$state = $assignment->store();

			if (!$state) {
				return $this->ajax->reject(JText::_('COM_EASYDISCUSS_ASSIGN_MODERATORS_SHOW_STORING_FAILED'));
			}

			$addLog = true;

			if ($state && $sendNotti) {
				// send notification to moderator when admin assigned post to them
				$post->notifyAssignedModerator($moderatorId, $post->id);
			}

			$post->assignee = ED::user($assignment->assignee_id);
		}

		if ($addLog) {
			// log activity
			$actiLib = ED::activity();
			$tmpl = $actiLib->getTemplate();
			$tmpl->setAction('post.moderator');
			$tmpl->setActor($assigner->id);
			$tmpl->setType('post', $post->id);
			$tmpl->setContent($oldAssignee, $newAssignee);
			$actiLib->log($tmpl);
		}


		$theme = ED::themes();
		$theme->set('moderator', $moderator);
		$contents = $theme->output('site/helpers/assignment/assignee');

		return $this->ajax->resolve($contents);
	}
}
