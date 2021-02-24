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
		$isNew = $id ? false : true;

		$subscription = ED::table('Subscribe');
		$subscription->load($id);

		$task = $this->getTask();

		$subscription->type = $this->input->get('type', 'site', 'word');
		$subscription->cid = $this->input->get('cid_' . $subscription->type, 0, 'int');

		$subscription->fullname = $this->input->get('fullname', '', 'string');
		$subscription->email = $this->input->get('email', '', 'email');

		// Fixed for Joomla 4 compatibility
		if (!$subscription->userid) {
			$subscription->userid = 0;
		}

		// If the subscription type is post or category, ensure that there is a cid
		if (($subscription->type == 'post' || $subscription->type == 'category') && !$subscription->cid) {

			if ($subscription->type == 'post') {
				ED::setMessage('COM_EASYDISCUSS_SUBSCRIPTION_PLEASE_SELECT_POST', ED_MSG_ERROR);
			}

			if ($subscription->type == 'category') {
				ED::setMessage('COM_EASYDISCUSS_SUBSCRIPTION_PLEASE_SELECT_CATEGORY', ED_MSG_ERROR);
			}

			$redirect = 'index.php?option=com_easydiscuss&view=subscription&layout=form&id=' . $subscription->id;

			return ED::redirect($redirect);
		}

		$subscription->store();

		if ($task == 'apply') {
			$redirect = 'index.php?option=com_easydiscuss&view=subscription&layout=form&id=' . $subscription->id;	
		}

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionString = 'COM_ED_ACTIONLOGS_UPDATED_SUBSCRIPTION';

		if ($subscription->type && $isNew) {
			$subType = strtoupper($subscription->type);
			$actionString = 'COM_ED_ACTIONLOGS_CREATED_' . $subType . '_SUBSCRIPTION';
		}

		$actionlog->log($actionString, 'subscription', array(
			'link' => 'index.php?option=com_easydiscuss&view=subscription&layout=form&id=' . $subscription->id,
			'userEmail' => $subscription->email
		));

		ED::setMessage('COM_EASYDISCUSS_SUBSCRIPTION_SAVED_SUCCESS', 'success');

		return ED::redirect($redirect);
	}

	public function remove()
	{
		$subs = $this->input->get('cid', '', 'POST');
		$message = '';
		$type = 'success';

		// log the current action into database.
		$actionlog = ED::actionlog();

		if (count($subs) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type = ED_MSG_ERROR;
		} else {
			$table = ED::table('Subscribe');
			
			foreach($subs as $sub) {
				$table->load($sub);

				$state = $table->delete();

				if (!$state) {
					ED::setMessage(JText::_('COM_EASYDISCUSS_REMOVING_SUBSCRIPTION_PLEASE_TRY_AGAIN_LATER'), ED_MSG_ERROR);
					return ED::redirect('index.php?option=com_easydiscuss&view=subscription');
				}

				$actionlog->log('COM_ED_ACTIONLOGS_DELETED_SUBSCRIPTION', 'subscription', array(
					'userEmail' => $table->email
				));
			}

			$message = JText::_('COM_EASYDISCUSS_SUBSCRIPTION_DELETED');
		}

		ED::setMessage($message, $type);

		return ED::redirect('index.php?option=com_easydiscuss&view=subscription');
	}

}
