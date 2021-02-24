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

class EasyDiscussViewSubscription extends EasyDiscussView
{
	/**
	 * Displays the subscription dialog window
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function form()
	{
		// Get the subscription type
		$type = $this->input->get('type', '', 'cmd');
		$cid = $this->input->get('cid', '', 'int');

		// Allowed subscription types
		$allowed = array('site', 'post', 'category');

		if (!in_array($type, $allowed)) {
			return;
		}

		$model = ED::model('Subscribe');

		// Determines if the user has subscribed to the site before.
		$interval = false;
		$subscription = $model->isSiteSubscribed($type, $this->my->email, $cid);

		if ($subscription) {
			$interval = $subscription->interval;
		}

		$theme = ED::themes();
		$theme->set('cid', $cid);
		$theme->set('type', $type);
		$theme->set('subscription', $subscription);
		$theme->set('interval', $interval);
		
		$output = $theme->output('site/subscription/dialogs/form');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays the subscription dialog window
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function unsubscribeDialog()
	{
		// Get the subscription type
		$type = $this->input->get('type', '', 'cmd');
		$cid = $this->input->get('cid', '', 'int');
		$sid = $this->input->get('sid', '', 'int');

		if (!$sid) {
			return;
		}

		$theme = ED::themes();
		$theme->set('cid', $cid);
		$theme->set('type', $type);
		$theme->set('sid', $sid);

		$namespace = 'site/subscription/dialogs/unsubscribe';
		$output = $theme->output($namespace);

		return $this->ajax->resolve($output);
	}

	/**
	 * Process user request to unsubscribe from subscription
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function unsubscribe()
	{
		ED::checktoken();

		// Get variables from post.
		$cid = $this->input->get('cid', 0, 'int');
		$type = $this->input->get('type', null);
		$sid = $this->input->get('sid', 0, 'int');

		if (!$sid) {
			die("no sid");
		}

		$sub = ED::table('Subscribe');
		$state = $sub->load($sid);

		if (!$state) {
			die("not found");
		}

		// Ensure that the user can truly delete
		if ($sub->member && $sub->userid != $this->my->id) {
			die();
		}

		$state = $sub->delete();

		if (!$state) {
			return $this->ajax->reject('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_FAILED_ERROR_DELETING_RECORDS');
		}

		$message = 'COM_EASYDISCUSS_UNSUBSCRIPTION_SITE_SUCCESS';
		
		if ($type == 'category') {
			$message = 'COM_EASYDISCUSS_UNSUBSCRIPTION_CATEGORY_SUCCESS';
		}

		if ($type == 'post') {
			$message = 'COM_ED_SUBSCRIPTION_UNSUBSCRIBE_DISCUSSION_SUCCESS';
		}
		
		$message = JText::_($message);

		return $this->ajax->resolve($message);
	}

	/**
	 * Post process after the user subscribes
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function process()
	{
		ED::checktoken();

		// Get variables from post.
		$cid = $this->input->get('cid', 0, 'int');
		$type = $this->input->get('type', null);
		$name = $this->input->get('subscribe_name', '', 'string');
		$email = $this->input->get('subscribe_email', '', 'string');

		// Allowed subscription types
		$allowed = array('site', 'post', 'category');

		if (!in_array($type, $allowed)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'));
		}

		if ($type == 'post') {

			// Load the post
			$post = ED::post($cid);

			if (!$post->id) {
				return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'));
			}

			if (!$post->canSubscribe()) {
				return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'));
			}
		}

		// default interval to weekly for site / cat, post to daily
		// if the email digest is disabled, make a 'instant' as a default interval
		// for now we have an option from backend to set default interval
		$emailDigestEnabled = $this->config->get('main_email_digest');
		$emailDigestInterval = $this->config->get('main_email_digest_interval', 'weekly');

		$interval = $emailDigestInterval;

		if ($type == 'post' || !$emailDigestEnabled) {
			$interval = 'instant';
		}

		// Apply filtering on the name.
		$filter = JFilterInput::getInstance();
		$name = $filter->clean($name, 'STRING');
		$email = EDJString::trim($email);
		$name = EDJString::trim($name);


		// Check for empty email
		if (empty($email)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_EMAIL_IS_EMPTY'));
		}

		// Check for empty name
		if (empty($name)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NAME_IS_EMPTY'));
		}


		if (!JMailHelper::isEmailAddress($email)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_INVALID_EMAIL'));
		}

		$model = ED::model('Subscribe');
		$subscription = $model->isSiteSubscribed($type, $email, $cid);

		$data = array();
		$data['type'] = $type;
		$data['userid'] = $this->my->id;
		$data['email'] = $email;
		$data['cid'] = $cid;
		$data['member'] = ($this->my->id) ? true : false;
		$data['name'] = ($this->my->id)? $this->my->name : $name;
		$data['interval'] = ($subscription) ? $subscription->interval : $interval;

		$message = JText::_('COM_ED_SUBSCRIBED_SUCCESSFULLY');
		$manageLink = '';

		if ($this->my->id) {
			$filter = ($type == 'category') ? '&filter=category' : '';
			$manageLink = EDR::_('view=subscription' . $filter);
		}

		if ($subscription) {
			return $this->ajax->resolve($message, $manageLink);
		}

		// If there is no subscription record for this user, add it here
		if (!$model->addSubscription($data)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SUBSCRIPTION_FAILED'));
		}

		return $this->ajax->resolve($message, $manageLink);
	}

	/**
	 * Renders the post process dialog
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function postProcess()
	{
		$contents = $this->input->get('contents', '', 'default');
		$manageLink = $this->input->get('manageLink', '', 'default');

		$theme = ED::themes();
		$theme->set('manageLink', $manageLink);
		$theme->set('contents', $contents);
		$output = $theme->output('site/subscription/dialogs/process');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the tab when viewing my subscriptions
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function renderTab()
	{
		// always reset the limitstart.
		$this->input->set('limitstart', 0);

		$type = $this->input->get('type', '', 'cmd');

		// Load subscription library.
		$subscription = ED::subscription();
		$model = ED::model('Subscribe');

		// Only allowed specific filter types
		$allowedFilters = ['category', 'post'];

		if (!in_array($type, $allowedFilters)) {
			die('not allowed');
		}

		$options = array(
			'userid' => $this->my->id,
			'type' => $type,
		);

		// Get posts subscriptions from the user.
		$items = $model->getSubscriptionBy($options);

		// Format the content base on the type
		$items = $subscription->format($items, $type);
		$pagination = $model->getPagination()->getPagesLinks('subscription', array('filter' => $type), true);

		$namespace = 'site/subscription/listings/posts';

		if ($type == 'category') {
			$namespace = 'site/subscription/listings/categories';
		}

		$theme = ED::themes();
		$theme->set('pagination', $pagination);
		$theme->set('items', $items);
		$contents = $theme->output($namespace);

		return $this->ajax->resolve($contents);
	}

	/**
	 * Toggles site wide subscription for user
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function toggleSiteSubscription()
	{
		$model = ED::model('subscribe');

		$result = $model->subscribeToggle($this->my->id);

		return $result;
	}

	/**
	 * Updates the subscription interval
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function updateSubscribeInterval()
	{
		$id = $this->input->get('id', '', 'cmd');
		$interval = $this->input->get('data', '', 'cmd');

		$model = ED::model('Subscribe');

		$result = $model->updateSubscriptionInterval($id, $interval);

		return $this->ajax->resolve($result);
	}

	/**
	 * Updates the subscription sorting behavior
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function updateSubscribeSort()
	{
		$id = $this->input->get('id', '', 'cmd');
		$sort = $this->input->get('data', '', 'cmd');

		$model = ED::model('Subscribe');

		$result = $model->updateSubscriptionSort($id, $sort);

		return $this->ajax->resolve($result);
	}

	/**
	 * Updates the subscription limit behavior
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function updateSubscribeCount()
	{
		$id = $this->input->get('id', '', 'cmd');
		$count = $this->input->get('data', '', 'cmd');

		$model = ED::model('Subscribe');

		$result = $model->updateSubscriptionCount($id, $count);

		return $this->ajax->resolve($result);
	}
}
