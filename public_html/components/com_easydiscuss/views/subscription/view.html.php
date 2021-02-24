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
	public function display($tpl = null)
	{
		// Ensure that the user is logged in
		ED::requireLogin();

		$registry = ED::registry();

		ED::setPageTitle(JText::_('COM_EASYDISCUSS_PAGETITLE_SUBSCRIPTIONS'));
		if (! EDR::isCurrentActiveMenu('subscription')) {
			$this->setPathway( JText::_('COM_EASYDISCUSS_BREADCRUMB_SUBSCRIPTIONS'));
		}

		// Load the user's profile
		$profile = ED::profile($this->my->id);

		// If profile is invalid, throw an error.
		if (!$profile->id || !$this->my->id) {
			throw ED::exception('COM_EASYDISCUSS_USER_ACCOUNT_NOT_FOUND', ED_MSG_ERROR);
		}

		$subscription = ED::subscription();
		$model = ED::model('Subscribe');

		// Get site subscriptions
		$siteSubscription = $model->getSubscriptionBy([
			'userid' => $profile->id, 
			'type' => 'site', 
			'pagination' => false
		]);

		$isSiteActive = false;
		$siteInterval = '';

		if (!empty($siteSubscription)) {
			$siteSubscription = $siteSubscription[0];

			$isSiteActive = $siteSubscription->state;
			$siteInterval = $siteSubscription->interval;
		}

		$filter = $this->input->get('filter', 'post', 'word');

		// Only allowed specific filter types
		$allowedFilters = ['category', 'post'];

		if (!in_array($filter, $allowedFilters)) {
			die('not allowed');
		}

		$options = [
			'userid' => $profile->id,
			'type' => $filter
		];

		// Get posts or categories subscriptions from the user.
		$items = $model->getSubscriptionBy($options);

		// Format the content base on the type
		$items = $subscription->format($items, $filter);

		// pagination. work in progress
		$pagination = $model->getPagination()->getPagesLinks('subscription', array('filter' => $filter));

		// Check if this user has all instant interval
		$allInstantSubscription = $model->allInstantSubscription($profile->id);

		$this->set('items', $items);
		$this->set('siteSubscription', $siteSubscription);
		$this->set('allInstantSubscription', $allInstantSubscription);		
		$this->set('isSiteActive', $isSiteActive);
		$this->set('siteInterval', $siteInterval);
		$this->set('profile', $profile);
		$this->set('pagination', $pagination);
		$this->set('filter', $filter);
		$this->set('user', $this->my);

		parent::display('subscription/listings/default');
	}
}
