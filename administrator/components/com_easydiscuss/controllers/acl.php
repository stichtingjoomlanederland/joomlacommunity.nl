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

jimport('joomla.application.component.controller');

class EasyDiscussControllerAcl extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.acls');

		$this->registerTask('apply', 'save');
	}

	public function cancel()
	{
		$this->app->redirect('index.php?option=com_easydiscuss&view=acls');
	}

	/**
	 * Process the ACL store
	 *
	 * @since	4.0
	 * @access	private
	 * @return
	 */
	public function save()
	{
		// Check for request forgeries
		ED::checkToken();

		$cid = $this->input->post->get('cid', null, 'POST');

		$redirect = 'index.php?option=com_easydiscuss&view=acls';

		if (!$cid) {
			$message = JText::_('COM_EASYDISCUSS_ACL_INVALID_ID_TYPE');

			ED::setMessage($message, 'error');

			return $this->app->redirect($redirect);
		}

		if ($this->getTask() == 'apply') {
			$redirect = 'index.php?option=com_easydiscuss&view=acls&layout=form&id=' . $cid;
		}

		$model = ED::model('Acl');
		$state = $model->deleteRuleset($cid, 'group');

		if (!$state) {
			$message = JText::_('COM_EASYDISCUSS_ACL_ERROR_UPDATE');
			ED::setMessage($message, 'error');

			return $this->app->redirect($redirect);
		}

		$post = $this->input->getArray('post');

		// Unset unecessary data.
		unset($post['task']);
		unset($post['option']);
		unset($post['controller']);
		unset($post['cid']);
		unset($post['name']);
		unset($post['type']);
		unset($post['boxchecked']);
		unset($post['view']);
		unset($post[ED::getToken()]);

		$data = array();

		foreach ($post as $key => $value) {
			$data[$key] = $value;
		}

		// Try to insert the new items
		$state = $model->insertRuleset($cid, 'group', $data);

		if (!$state) {
			$message = JText::_('COM_EASYDISCUSS_ACL_ERROR_SAVING');

			ED::setMessage($message, 'error');

			return $this->app->redirect($redirect);
		}

		$message = JText::_('COM_EASYDISCUSS_ACL_SUCCESSFULLY_SAVED');
		ED::setMessage($message, 'success');

		return $this->app->redirect($redirect);
	}
}
