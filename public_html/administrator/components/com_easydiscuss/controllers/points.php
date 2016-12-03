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

class EasyDiscussControllerPoints extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.points');

		// Need to explicitly define this in Joomla 3.0
		$this->registerTask('unpublish', 'unpublish');
		$this->registerTask('publish', 'unpublish');
		$this->registerTask('saveNew', 'save');
	}

	public function add()
	{
		return $this->app->redirect('index.php?option=com_easydiscuss&view=points&layout=form');
	}

	public function remove()
	{
		ED::checkToken();
		$ids = $this->input->get('cid', '', 'var');

		foreach ($ids as $id) {
			$point	= ED::table('Points');
			$point->load($id);
			$point->delete();
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_BADGES_DELETED'), 'success');
		return $this->app->redirect('index.php?option=com_easydiscuss&view=points');
	}

	public function unpublish()
	{
		ED::checkToken();

		$point = ED::table('Points');
		$ids = $this->input->get('cid', '', 'var');
		$task = $this->input->get('task', '', 'var');
		$state = $task == 'publish' ? 1 : 0;

		foreach ($ids as $id) {
			$id	= (int) $id;
			$point->load($id);
			$point->set('published', $state);
			$point->store();
		}

		$message = $state ? JText::_( 'COM_EASYDISCUSS_POINTS_PUBLISHED' ) : JText::_( 'COM_EASYDISCUSS_POINTS_UNPUBLISHED' );

		ED::setMessage($message, 'success');
		return $this->app->redirect('index.php?option=com_easydiscuss&view=points');
	}

	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_easydiscuss&view=points');
	}

	public function rules()
	{
		return $this->app->redirect( 'index.php?option=com_easydiscuss&view=rules&from=points');
	}

	public function save()
	{
		ED::checkToken();

		$point = ED::table('Points');
		$id = $this->input->get('id', 0, 'int');
		$task = $this->input->get('task', '', 'var');

		$point->load($id);

		$post = JRequest::get('post');
		
		$point->bind($post);

		if (empty($point->created)) {
			$point->created = ED::date()->toSql();
		}

		// Store the badge
		$point->store();

		$message = !empty($id) ? JText::_('COM_EASYDISCUSS_POINTS_UPDATED') : JText::_('COM_EASYDISCUSS_POINTS_CREATED');

		$url = 'index.php?option=com_easydiscuss&view=points';

		if ($task == 'saveNew') {
			$url = 'index.php?option=com_easydiscuss&view=points&layout=form';
		}

		ED::setMessage($message, 'success');
		return $this->app->redirect($url);
	}
}
