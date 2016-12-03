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

class EasyDiscussControllerTags extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.tags');

		$this->registerTask('unpublish', 'unpublish');
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
	}

	public function save()
	{
		$message = '';
		$type = 'success';
		$task = $this->getTask();
		$url = 'index.php?option=com_easydiscuss&view=tags';

		// Retrive the post. 
		if (JRequest::getMethod() == 'POST') {
			
			$post = JRequest::get('post');

			// Retrieve the tagId.
			$tagId = $this->input->get('tagid', '', 'var');

			// Validation.
			if (empty($post['title'])) {
				ED::setMessage(JText::_('COM_EASYDISCUSS_EMPTY_TAG_TITLE'), 'error');
				if (!$tagId) {
					return $this->app->redirect(EDR::_($url . '&layout=form', false));
				}
				return $this->app->redirect(EDR::_($url . '&layout=form&id=' . $tagId, false));
			}

			// Retrieve the current user_id
			$post['user_id'] = $this->my->id;

			$tag = ED::table('tags');

			// If the tagId is provided, then it is a edit.
			if (!empty($tagId)) {
				// Load the tagId.
				$tag->load($tagId);

			} else {
				// If the tagId is not provided, then we'll need to search tags with similar name.
				// If found, return.
				$tagModel = ED::model('Tags');
				$result = $tagModel->searchTag($tag->title);

				if (!empty($result)) {
					ED::setMessage(JText::_('COM_EASYDISCUSS_TAG_EXISTS'), 'error');
					$this->app->redirect($url);
				}
			}

			$tag->bind($post);

			$tag->title = JString::trim($tag->title);
			$tag->alias = JString::trim($tag->alias);

			$status = $tag->store();

			if (!$status) {
				JError::raiseError(500, $tag->getError());
			} else {
				$message = JText::_('COM_EASYDISCUSS_TAG_SAVED');
			}

			$mergeTo = isset($post['mergeTo']) ? (int) $post['mergeTo'] : 0;

			$mergeToTag	= ED::table('Tags');
			$mergeToTag->load($mergeTo);

			if ($mergeToTag->id > 0 && $tag->id > 0) {
				// Move to merge tag id
				$db	= ED::db();

				// Find posts tagged in both id
				$query	= 'SELECT a.id FROM #__discuss_posts_tags AS a'
						. ' LEFT JOIN #__discuss_posts_tags AS b ON b.post_id = a.post_id'
						. ' WHERE a.tag_id = ' . $db->quote($tag->id)
						. ' AND b.tag_id = ' . $db->quote($mergeToTag->id)
						. ' GROUP BY a.post_id';
				$db->setQuery($query);
				$excludeIds = $db->loadResultArray();

				// Do not update post having both tags, let $table->delete() handle them
				$query	= 'UPDATE `#__discuss_posts_tags`'
						. ' SET `tag_id` = ' . $db->quote($mergeToTag->id)
						. ' WHERE `tag_id` = ' . $db->quote($tag->id);

				if (count($excludeIds) > 0) {
					JArrayHelper::toInteger($excludeIds);

					$query .= ' AND `id` NOT IN (' . implode(',', $excludeIds) . ')';
				}

				$db->setQuery($query);
				$db->query();

				$tag->delete();
			}
		} else {
			$message = JText::_('COM_EASYDISCUSS_INVALID_FORM_METHOD');
			$type = 'error';
		}

		ED::setMessage($message, $type);

		if ($task == 'save2new') {
			return $this->app->redirect($url . '&layout=form');
		}

		if ($task == 'apply') {
			return $this->app->redirect($url . '&layout=form&id=' . $tag->id);
		}

		return $this->app->redirect($url);
	}

	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_easydiscuss&view=tags');
	}

	public function remove()
	{
		$tags = $this->input->get('cid', '', 'POST');

		if (empty($tags)) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_TAG_ID'), 'error');
			return $this->app->redirect('index.php?option=com_easydiscuss&view=tags');
		}

		$table = ED::table('Tags');

		foreach ($tags as $tag) {
			$table->load($tag);

			if (!$table->delete()) {
				ED::setMessage(JText::_('COM_EASYDISCUSS_REMOVE_TAG_ERROR'), 'error');
				return $this->app->redirect('index.php?option=com_easydiscuss&view=tags');
			}

		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_TAG_DELETED'), 'success');

		return $this->app->redirect('index.php?option=com_easydiscuss&view=tags');
	}

	public function publish()
	{
		$tags = $this->input->get('cid', array(0), 'POST');
		$message = '';
		$type = 'success';

		if (count($tags) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_TAG_ID');
			$type = 'error';
		} else {
			$model = ED::model('Tags');

			if ($model->publish($tags, 1)) {
				$message = JText::_('COM_EASYDISCUSS_TAG_PUBLISHED');
			} else {
				$message = JText::_('COM_EASYDISCUSS_TAG_PUBLISH_ERROR');
				$type = 'error';
			}

		}

		ED::setMessage($message, $type);

		$this->app->redirect('index.php?option=com_easydiscuss&view=tags');
	}

	public function unpublish()
	{
		$tags = $this->input->get('cid', array(0), 'POST');
		$message = '';
		$type = 'success';

		if (count($tags) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_TAG_ID');
			$type = 'error';
		} else {
			$model = ED::model('Tags');

			if ($model->publish($tags, 0)) {
				$message = JText::_('COM_EASYDISCUSS_TAG_UNPUBLISHED');
			} else {
				$message = JText::_('COM_EASYDISCUSS_TAG_UNPUBLISH_ERROR');
				$type = 'error';
			}
		}

		ED::setMessage($message, $type);

		$this->app->redirect('index.php?option=com_easydiscuss&view=tags');
	}
}
