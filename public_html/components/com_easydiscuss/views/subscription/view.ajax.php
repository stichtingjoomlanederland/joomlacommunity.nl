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

require_once(DISCUSS_ROOT . '/views/views.php');

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

		$namespace = 'site/subscription/dialog.subscribe.' . $type;
		$output = $theme->output($namespace);

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

		if (! $sid) {
			// TODO: show error
			return;
		}

		$theme = ED::themes();
		$theme->set('cid', $cid);
		$theme->set('type', $type);
		$theme->set('sid', $sid);

		$namespace = 'site/subscription/dialog.unsubscribe';
		$output = $theme->output($namespace);

		return $this->ajax->resolve($output);
	}

	/**
	 * process un-subscribe
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

		if (! $sid) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'));
		}

		$sub = ED::table('Subscribe');
		$state = $sub->load($sid);

		if (! $state) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SUBSCRIPTION_NOT_FOUND'));
		}

		$state = $sub->delete();

		if (! $state) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_FAILED_ERROR_DELETING_RECORDS'));
		}

		$message = JText::_('COM_EASYDISCUSS_UNSUBSCRIPTION_SITE_SUCCESS');
		if ($type == 'category') {
			$message = JText::_('COM_EASYDISCUSS_UNSUBSCRIPTION_CATEGORY_SUCCESS');
		} else if ($type == 'post') {
			$message = JText::_('COM_ED_SUBSCRIPTION_UNSUBSCRIBE_DISCUSSION_SUCCESS');
		}

		return $this->ajax->resolve($message);

	}

	/**
	 * process subscription
	 *
	 * @since	4.0
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
		// $interval = $this->input->get('subscription_interval' ,'weekly', 'string');

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
		$email = JString::trim($email);
		$name = JString::trim($name);


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

		$withSetting = ($this->my->id) ? '_WITH_LINK' : '';

		$successMsg = JText::_('COM_EASYDISCUSS_SUBSCRIPTION_SUBSCRIBED_SUCCESSFULLY');

		if ($this->my->id) {

			$filter = ($type == 'category') ? '&filter=category' : '';
			$settingLink = EDR::_('view=subscription' . $filter);
			$successMsg = JText::sprintf('COM_EASYDISCUSS_SUBSCRIPTION_SUBSCRIBED_SUCCESSFULLY_WITH_LINK', $settingLink);
		}

		if ($subscription) {
			return $this->ajax->resolve($successMsg);
		}

		// If there is no subscription record for this user, add it here
		if (!$model->addSubscription($data)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SUBSCRIPTION_FAILED'));
		}

		return $this->ajax->resolve($successMsg);
	}

	public function tab()
	{
		// always reset the limitstart.
		JRequest::setVar('limitstart', 0);

		$type = $this->input->get('type', '', 'cmd');
		$id = $this->input->get('id', '', 'int');

		// Load subscription library.
		$subscription = ED::subscription();
		$model = ED::model('Subscribe');

		$options = array(
			'userid' => $id,
			'type' => $type,
			);

		// Get posts subscriptions from the user.
		$content = $model->getSubscriptionBy($options);

		// Format the content base on the type
		$content = $subscription->format($content, $type);

		$namespace = 'site/subscription/default.item';

		if ($type == 'category') {
			$namespace = 'site/subscription/default.category';
		}

		$pagination = $model->getPagination()->getPagesLinks('subscription', array('filter' => $type), true);

		$theme = ED::themes();

		$contents = '';

		foreach ($content as $post) {
			$theme->set('post', $post);
			$contents .= $theme->output($namespace);
		}

		return $this->ajax->resolve($contents, $pagination);
	}

	public function subscribeToggle()
	{
		$id = $this->input->get('id', '', 'cmd');

		// create a new function to enable or disable subscriptions notification.
		// work in progress
		$model = ED::model('subscribe');

		$result = $model->subscribeToggle($id);

		return $result;
	}

	public function updateSubscribeInterval()
	{
		$id = $this->input->get('id', '', 'cmd');
		$interval = $this->input->get('data', '', 'cmd');

		$model = ED::model('Subscribe');

		$result = $model->updateSubscriptionInterval($id, $interval);

		return $this->ajax->resolve($result);
	}

	public function updateSubscribeSort()
	{
		$id = $this->input->get('id', '', 'cmd');
		$sort = $this->input->get('data', '', 'cmd');

		$model = ED::model('Subscribe');

		$result = $model->updateSubscriptionSort($id, $sort);

		return $this->ajax->resolve($result);
	}

	public function updateSubscribeCount()
	{
		$id = $this->input->get('id', '', 'cmd');
		$count = $this->input->get('data', '', 'cmd');

		$model = ED::model('Subscribe');

		$result = $model->updateSubscriptionCount($id, $count);

		return $this->ajax->resolve($result);
	}
}
