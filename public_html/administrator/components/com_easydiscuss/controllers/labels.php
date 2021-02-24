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

class EasyDiscussControllerLabels extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.labels');

		$this->registerTask('apply', 'save');
		$this->registerTask('save', 'save');
		$this->registerTask('save2new', 'save');

		$this->registerTask('publish', 'publish');
		$this->registerTask('unpublish', 'publish');

		$this->registerTask('remove', 'delete');
	}

	/**
	 * Triggered when we need to store a new label or save an existing label
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function save()
	{
		ED::checkToken();

		// Get posted data
		$post = $this->input->getArray('post');

		$id = $this->input->get('id', 0, 'int');
		$isNew = $id ? false : true;

		$label = ED::table('Labels');
		$label->load($id);

		$post = $this->input->getArray('post');

		if (!isset($post['title']) || trim($post['title']) == "") {
			ED::setMessage('COM_ED_POST_LABEL_EMPTY_TITLE_MESSAGE', ED_MSG_ERROR);

			return $this->toRedirect($label->id);
		}

		$model = ED::model('PostLabels');

		if ($model->isExists('title', trim($post['title']), $label->id)) {
			ED::setMessage('COM_ED_POST_LABEL_DUPLICATE_TITLE_MESSAGE', ED_MSG_ERROR);

			return $this->toRedirect($label->id);
		}

		if (!isset($post['colour']) || trim($post['colour']) == "" ) {
			ED::setMessage('COM_ED_POST_LABEL_EMPTY_COLOUR_MESSAGE', ED_MSG_ERROR);

			return $this->toRedirect($label->id);
		}

		$label->title = trim($post['title']);
		$label->colour = $post['colour'];
		$label->published = $post['published'];
		$label->created = ED::date()->toSql();
		$label->store();

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlogMsg = $isNew ? 'COM_ED_ACTIONLOGS_POSTLABEL_CREATED' : 'COM_ED_ACTIONLOGS_POSTLABEL_UPDATED';

		$actionlog->log($actionlogMsg, 'postLabel', array(
			'link' => 'index.php?option=com_easydiscuss&view=labels&layout=form&id=' . $label->id,
			'postLabelTitle' => JText::_($label->title)
		));

		$task = $this->getTask();
		$toForm = false;
		$id = $label->id;

		if ($task == 'save2new') {
			$toForm = true;

			$id = null;
		} 

		if ($task == 'apply') {
			$toForm = true;
		} 

		if ($task == 'save') {
			$toForm = false;
		}

		$message = !$isNew ? JText::_('COM_ED_POST_LABEL_UPDATED') : JText::_('COM_ED_POST_LABEL_CREATED');

		ED::setMessage($message, 'success');
		$this->toRedirect($id, $toForm);
	}

	/**
	 * Perform the redirection.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function toRedirect($id, $toForm = true)
	{
		$url = 'index.php?option=com_easydiscuss&view=labels';

		if ($toForm) {
			$url .= '&layout=form';
		}

		if ($toForm && $id) {
			$url .= '&id=' . $id;
		}

		ED::redirect($url);
	}

	/**
	 * Process the delete.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function delete()
	{
		$ids = $this->input->get('cid', array(), 'array');
		$table = ED::table('Labels');
		$model = ED::model('PostLabels');

		foreach ($ids as $id) {
			$table->load($id);

			if (!$table->id) {
				continue;
			}

			$total = $model->getTotalPosts($table->id);

			// Do not delete it if it still associated with other questions
			if ($total) {
				$message = JText::sprintf('COM_ED_POST_LABEL_DELETE_ERROR_MESSAGE', $table->title);
				ED::setMessage($message, ED_MSG_ERROR);

				return $this->toRedirect(null, false);
			}

			$table->delete();
		}

		ED::setMessage('COM_ED_POST_LABEL_DELETE_SUCCESS_MESSAGE', 'success');

		$this->toRedirect(null, false);
	}

	/**
	 * Process the publish/unpublish.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function publish()
	{
		$label = ED::table('Labels');
		$ids = $this->input->get('cid', array(), 'array');
		$task = $this->getTask();
		$publishState = $task == 'publish' ? 1 : 0;

		foreach ($ids as $id) {
			$label->load($id);

			if (!$label->id) {
				continue;
			}

			$label->published = $publishState;
			$state = $label->store();

			if ($state) {
				// log the current action into database.
				$actionlog = ED::actionlog();
				$publishConstant = strtoupper($task);

				$actionlog->log('COM_ED_ACTIONLOGS_POSTLABEL_' . $publishConstant, 'postLabel', array(
					'link' => 'index.php?option=com_easydiscuss&view=labels&layout=form&id=' . $label->id,
					'postLabelTitle' => JText::_($label->title)
				));
			}
		}

		$message = $state ? JText::_('COM_ED_POST_LABELS_PUBLISHED') : JText::_('COM_ED_POST_LABELS_UNPUBLISHED');

		ED::setMessage($message, 'success');

		$this->toRedirect(null, false);
	}
}