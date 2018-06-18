<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussControllerSubscription extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 'save');

		$this->checkAccess('discuss.manage.subscriptions');
	}

	/**
	 * Saves changes to an existing subscription
	 *
	 * @since	4.0.14
	 * @access	public
	 */
	public function save()
	{
		$id = $this->input->get('id', 0, 'int');

		$redirect = 'index.php?option=com_easydiscuss&view=subscription';

		$subscription = ED::table('Subscribe');
		$subscription->load($id);

		$task = $this->getTask();

		if ($task == 'apply') {
			$redirect = 'index.php?option=com_easydiscuss&view=subscription&layout=form&id=' . $subscription->id;	
		}

		$subscription->type = $this->input->get('type', 'site', 'word');
		$subscription->cid = $this->input->get('cid_' . $subscription->type, 0, 'int');
		
		$subscription->fullname = $this->input->get('fullname', '', 'string');
		$subscription->email = $this->input->get('email', '', 'email');

		// If the subscription type is post or category, ensure that there is a cid
		if (($subscription->type == 'post' || $subscription->type == 'category') && !$subscription->cid) {

			if ($subscription->type == 'post') {
				ED::setMessage('COM_EASYDISCUSS_SUBSCRIPTION_PLEASE_SELECT_POST', 'error');
			}

			if ($subscription->type == 'category') {
				ED::setMessage('COM_EASYDISCUSS_SUBSCRIPTION_PLEASE_SELECT_CATEGORY', 'error');
			}

			$redirect = 'index.php?option=com_easydiscuss&view=subscription&layout=form&id=' . $subscription->id;

			return $this->app->redirect($redirect);
		}

		$subscription->store();

		ED::setMessage('COM_EASYDISCUSS_SUBSCRIPTION_SAVED_SUCCESS', 'success');

		return $this->app->redirect($redirect);
	}

	public function remove()
	{
		$subs = $this->input->get('cid', '', 'POST');
		$message = '';
		$type = 'success';

		if (count($subs) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type = 'error';
		} else {
			$table = ED::table('Subscribe');
			
			foreach($subs as $sub) {
				$table->load($sub);

				if (!$table->delete()) {
					ED::setMessage(JText::_('COM_EASYDISCUSS_REMOVING_SUBSCRIPTION_PLEASE_TRY_AGAIN_LATER'), 'error');
					return $this->app->redirect('index.php?option=com_easydiscuss&view=subscription');
				}
			}

			$message = JText::_('COM_EASYDISCUSS_SUBSCRIPTION_DELETED');
		}

		ED::setMessage($message, $type);

		return $this->app->redirect('index.php?option=com_easydiscuss&view=subscription');
	}

}
