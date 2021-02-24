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
		ED::checkToken();

		$post = $this->input->getArray('post');
		$tagId = $this->input->get('tagid', '', 'int');
		$task = $this->getTask();

		$type = 'success';
		$url = 'index.php?option=com_easydiscuss&view=tags';

		$tagTitle = $post['title'];

		if (!$tagTitle) {
			ED::setMessage('COM_EASYDISCUSS_EMPTY_TAG_TITLE', ED_MSG_ERROR);
			
			if (!$tagId) {
				return ED::redirect(EDR::_($url . '&layout=form', false));
			}

			return ED::redirect(EDR::_($url . '&layout=form&id=' . $tagId, false));
		}

		$tag = ED::table('tags');
		$tagModel = ED::model('Tags');

		// If the tagId is provided, then it is a edit.
		if (!empty($tagId)) {
			// Load the tagId.
			$tag->load($tagId);

		} else {
			// If the tagId is not provided, then we'll need to search tags with similar name.
			// If found, return.
			$result = $tagModel->searchTag($tag->title);

			if (!empty($result)) {
				ED::setMessage(JText::_('COM_EASYDISCUSS_TAG_EXISTS'), ED_MSG_ERROR);
				return ED::redirect($url);
			}
		}

		$tag->user_id = $this->my->id;
		$tag->bind($post, array(), true);

		$tag->title = EDJString::trim($tag->title);
		$tag->alias = EDJString::trim($tag->alias);

		$status = $tag->store();

		if (!$status) {
			ED::setMessage($tag->getError(), ED_MSG_ERROR);
			return ED::redirect($url);
		}

		$mergeTagId = isset($post['mergeTo']) ? (int) $post['mergeTo'] : 0;

		$mergeTag = ED::table('Tags');
		$mergeTag->load($mergeTagId);

		// Only process this if the form has the merge tag id
		if ($mergeTag->id) {
			$tagModel->mergeTag($mergeTag, $tag);
		}

		ED::setMessage('COM_EASYDISCUSS_TAG_SAVED', $type);

		$actionString = $tagId ? 'COM_ED_ACTIONLOGS_UPDATED_TAG' : 'COM_ED_ACTIONLOGS_CREATED_TAG' ;

		$actionlog = ED::actionlog();
		$actionlog->log($actionString, 'tag', array(
			'link' => 'index.php?option=com_easydiscuss&view=tags&layout=form&id=' . $tag->id,
			'tagTitle' => JText::_($tag->title)
		));	

		if ($task == 'save2new') {
			return ED::redirect($url . '&layout=form');
		}

		if ($task == 'apply') {
			return ED::redirect($url . '&layout=form&id=' . $tag->id);
		}

		return ED::redirect($url);
	}

	public function cancel()
	{
		return ED::redirect('index.php?option=com_easydiscuss&view=tags');
	}

	public function remove()
	{
		$tags = $this->input->get('cid', '', 'POST');

		if (empty($tags)) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_TAG_ID'), ED_MSG_ERROR);
			return ED::redirect('index.php?option=com_easydiscuss&view=tags');
		}

		$table = ED::table('Tags');

		foreach ($tags as $tag) {
			
			$table->load($tag);

			$state = $table->delete();

			if (!$state) {
				ED::setMessage(JText::_('COM_EASYDISCUSS_REMOVE_TAG_ERROR'), ED_MSG_ERROR);
				return ED::redirect('index.php?option=com_easydiscuss&view=tags');
			}
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_TAG_DELETED'), 'success');

		return ED::redirect('index.php?option=com_easydiscuss&view=tags');
	}

	public function publish()
	{
		$tags = $this->input->get('cid', array(0), 'POST');

		$redirect = 'index.php?option=com_easydiscuss&view=tags';

		if (!$tags) {
			ED::setMessage('COM_EASYDISCUSS_INVALID_TAG_ID', ED_MSG_ERROR);
			return ED::redirect($redirect);
		}

		$model = ED::model('Tags');
		$state = $model->publish($tags, 1);

		$message = JText::_('COM_EASYDISCUSS_TAG_PUBLISHED');
		$type = 'success';

		if (!$state) {
			$message = JText::_('COM_EASYDISCUSS_TAG_PUBLISH_ERROR');
			$type = ED_MSG_ERROR;
		}

		ED::setMessage($message, $type);

		return ED::redirect($redirect);
	}

	public function unpublish()
	{
		$tags = $this->input->get('cid', array(0), 'POST');

		$redirect = 'index.php?option=com_easydiscuss&view=tags';

		if (!$tags) {
			ED::setMessage('COM_EASYDISCUSS_INVALID_TAG_ID', ED_MSG_ERROR);
			return ED::redirect($redirect);
		}

		$model = ED::model('Tags');
		$state = $model->publish($tags, 0);

		$message = JText::_('COM_EASYDISCUSS_TAG_UNPUBLISHED');
		$type = 'success';

		if (!$state) {
			$message = JText::_('COM_EASYDISCUSS_TAG_UNPUBLISH_ERROR');
			$type = ED_MSG_ERROR;
		}

		ED::setMessage($message, $type);

		return ED::redirect($redirect);
	}
}
