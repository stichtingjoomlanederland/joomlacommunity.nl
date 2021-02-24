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

class EasyDiscussControllerPoints extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.points');

		// Need to explicitly define this in Joomla 3.0
		$this->registerTask('unpublish', 'togglePublish');
		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('saveNew', 'save');
	}

	public function add()
	{
		return ED::redirect('index.php?option=com_easydiscuss&view=points&layout=form');
	}

	public function remove()
	{
		ED::checkToken();
		$ids = $this->input->get('cid', '', 'array');

		$point = ED::table('Points');

		foreach ($ids as $id) {
			$point->load($id);
			$point->delete();
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_POINTS_DELETED'), 'success');
		return ED::redirect('index.php?option=com_easydiscuss&view=points');
	}

	public function togglePublish()
	{
		ED::checkToken();

		// Get the current task
		$task = $this->getTask();
		$ids = $this->input->get('cid', '', 'array');
		$state = $task == 'publish' ? 1 : 0;

		$point = ED::table('Points');

		foreach ($ids as $id) {
			$point->load((int) $id);
			$point->$task();
		}

		$message = $task == 'publish' ? JText::_('COM_EASYDISCUSS_POINTS_PUBLISHED') : JText::_('COM_EASYDISCUSS_POINTS_UNPUBLISHED');

		ED::setMessage($message, 'success');
		return ED::redirect('index.php?option=com_easydiscuss&view=points');
	}

	public function cancel()
	{
		return ED::redirect('index.php?option=com_easydiscuss&view=points');
	}

	public function rules()
	{
		return ED::redirect( 'index.php?option=com_easydiscuss&view=rules&from=points');
	}

	public function save()
	{
		ED::checkToken();

		$id = $this->input->get('id', 0, 'int');
		$isNew = $id ? false : true;
		$url = 'index.php?option=com_easydiscuss&view=points';

		// Get the current task
		$task = $this->getTask();

		$point = ED::table('Points');
		$point->load($id);

		$post = $this->input->post->getArray();
		$point->bind($post);

		if (empty($point->created)) {
			$point->created = ED::date()->toSql();
		}

		// Store the point
		$point->store();

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlogMsg = $isNew ? 'COM_ED_ACTIONLOGS_POINTRULES_CREATED' : 'COM_ED_ACTIONLOGS_POINTRULES_UPDATED';

		$actionlog->log($actionlogMsg, 'points', array(
			'link' => $url . '&layout=form&id=' . $point->id,
			'pointRuleTitle' => JText::_($point->title)
		));

		$message = $isNew ? JText::_('COM_EASYDISCUSS_POINTS_CREATED') : JText::_('COM_EASYDISCUSS_POINTS_UPDATED');

		if ($task == 'saveNew') {
			$url = 'index.php?option=com_easydiscuss&view=points&layout=form';
		}

		ED::setMessage($message, 'success');
		return ED::redirect($url);
	}
}
