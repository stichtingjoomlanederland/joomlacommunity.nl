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

class EasyDiscussControllerLabels extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.labels');

		$this->registerTask('add', 'edit');
		$this->registerTask('savepublishnew', 'save');
	}

	public function save()
	{
		$message = '';
		$type = 'success';

		if (JRequest::getMethod() == 'POST') {

			$post = JRequest::get('post');

			if (empty($post['title'])) {
				$this->app->enqueueMessage(JText::_('COM_EASYDISCUSS_INVALID_LABEL'), 'error');
				$url = 'index.php?option=com_easydiscuss&view=labels';
				return $this->app->redirect(JRoute::_($url, false));
			}

			$post['created_user_id'] = $this->my->id;
			$labelId = $this->input->get('label_id', '', 'var');
			$label = ED::table('Label');

			$label->load($labelId);
			$label->bind($post);

			$label->title = JString::trim($label->title);

			if (!$label->store()) {
				JError::raiseError(500, $label->getError());
			} else {
				$message = JText::_('COM_EASYDISCUSS_LABEL_SAVED');
			}
		} else {
			$message = JText::_('COM_EASYDISCUSS_INVALID_FORM_METHOD');
			$type = 'error';
		}

		ED::setMessage($message, $type);
		$saveNew = $this->input->get('savenew', false, 'bool');
		$task = $this->input->get('task', '', 'cmd');
		$saveNew = $task == 'savePublishNew';
		
		if ($saveNew) {
			return $this->app->redirect( 'index.php?option=com_easydiscuss&view=labels&task=labels.edit');
		}

		$this->app->redirect('index.php?option=com_easydiscuss&view=labels');
	}

	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_easydiscuss&view=labels');
	}

	public function edit()
	{
		$this->input->set('view', 'label');
		$this->input->set('labelid', $this->input->get('labelid', '', 'REQUEST'));

		parent::display();
	}

	public function remove()
	{
		$labels = $this->input->get('cid', '', 'POST');
		$message = '';
		$type = 'success';

		if (empty($labels)) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_LABEL_ID');
			$type = 'error';
		} else {
			$table = ED::table('Label');

			foreach ($labels as $label) {
				
				$table->load($label);

				if (!$table->delete()) {
					$message = JText::_('COM_EASYDISCUSS_REMOVE_LABEL_ERROR');
					$type = 'error';

					ED::setMessage($message, $type);

					return $this->app->redirect('index.php?option=com_easydiscuss&view=labels');
				}
			}

			$message = JText::_('COM_EASYDISCUSS_LABEL_DELETED');
		}

		ED::setMessage($message, $type);

		return $this->app->redirect('index.php?option=com_easydiscuss&view=labels');
	}

	public function publish()
	{
		$labels = $this->input->get('cid', array(0), 'POST');
		$message = '';
		$type = 'success';

		if (count($labels) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_LABEL_ID');
			$type = 'error';
		} else {
			$model = ED::model('Labels');

			if ($model->publish($labels, 1)) {
				$message = JText::_('COM_EASYDISCUSS_LABEL_PUBLISHED');
			} else {
				$message = JText::_('COM_EASYDISCUSS_LABEL_PUBLISH_ERROR');
				$type = 'error';
			}
		}

		ED::setMessage($message , $type);

		return $this->app->redirect('index.php?option=com_easydiscuss&view=labels');
	}

	public function unpublish()
	{
		$labels = $this->input->get('cid', array(0), 'POST');
		$message = '';
		$type = 'success';

		if (count($labels) <= 0){
			$message = JText::_('COM_EASYDISCUSS_INVALID_LABEL_ID');
			$type = 'error';
		} else {
			$model = ED::model('Labels');

			if ($model->publish($labels, 0)) {
				$message = JText::_('COM_EASYDISCUSS_LABEL_UNPUBLISHED');
			} else {
				$message = JText::_('COM_EASYDISCUSS_LABEL_UNPUBLISH_ERROR');
				$type = 'error';
			}
		}

		ED::setMessage($message, $type);

		return $this->app->redirect('index.php?option=com_easydiscuss&view=labels');
	}

	public function orderdown()
	{
		// Check for request forgeries
		ED::checkToken();

		self::orderLabel(1);
	}

	public function orderup()
	{
		// Check for request forgeries
		ED::checkToken();

		self::orderLabel(-1);
	}

	public function orderLabel($direction)
	{
		// Check for request forgeries
		ED::checkToken();

		// Initialize variables
		$db	= ED::db();
		$cid = $this->input->get('cid', array(), 'post', 'array');

		if (isset($cid[0])) {
			$row = ED::table('Label');
			$row->load((int) $cid[0]);
			$row->move($direction);
		}

		return $this->app->redirect( 'index.php?option=com_easydiscuss&view=labels');
	}

	public function saveOrder()
	{
		// Check for request forgeries
		ED::checkToken();

		$row = ED::table('Label');
		$row->rebuildOrdering();

		$message = JText::_('COM_EASYDISCUSS_LABELS_ORDERING_SAVED');
		$type = 'message';
		ED::setMessage($message , $type );
		return $this->app->redirect( 'index.php?option=com_easydiscuss&view=labels');
	}
}
