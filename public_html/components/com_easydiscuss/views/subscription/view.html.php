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

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewSubscription extends EasyDiscussView
{
	public function display($tpl = null)
	{
		// Ensure that the user is logged in
		ED::requireLogin();

		$registry = ED::registry();
		$filter = $this->input->get('filter', 'post', 'word');

		ED::setPageTitle(JText::_('COM_EASYDISCUSS_PAGETITLE_SUBSCRIPTIONS'));
		if (! EDR::isCurrentActiveMenu('subscription')) {
			$this->setPathway( JText::_('COM_EASYDISCUSS_BREADCRUMB_SUBSCRIPTIONS'));
		}

		// Load the user's profile
		$profile = ED::profile($this->my->id);

		// If profile is invalid, throw an error.
		if (!$profile->id || !$this->my->id) {
			return JError::raiseError(404, JText::_('COM_EASYDISCUSS_USER_ACCOUNT_NOT_FOUND'));
		}

		$subscription = ED::subscription();
		$model = ED::model('Subscribe');

		// Get site subscriptions
		$siteSubscribe = $model->getSubscriptionBy(array('userid' => $profile->id, 'type' => 'site', 'pagination' => false));

		$isSiteActive = false;
		$siteInterval = '';

		if (!empty($siteSubscribe)) {
			$isSiteActive = $siteSubscribe[0]->state;
			$siteInterval = $siteSubscribe[0]->interval;
		}

		$options = array(
			'userid' => $profile->id,
			'type' => $filter
			);

		// Get posts or categories subscriptions from the user.
		$postSubscribe = $model->getSubscriptionBy($options);

		// Format the content base on the type
		$postSubscribe = $subscription->format($postSubscribe, $filter);

		$namespace = 'site/subscription/default.item';

		if ($filter == 'category') {
			$namespace = 'site/subscription/default.category';
		}

		// Get post subscriptions graph data.
		$postGraph = $subscription->getGraphSubscription($profile->id, 'post');

		// Get category subscriptions graph data.
		$categoryGraph = $subscription->getGraphSubscription($profile->id, 'category');

		// json encode the graph data
		$postDataSet = json_encode($postGraph[0]->count);
		$label = json_encode($postGraph[1]);
		$categoryDataSet = json_encode($categoryGraph[0]->count);

		// pagination. work in progress
		$pagination = $model->getPagination()->getPagesLinks('subscription', array('filter' => $filter));

		// Check if this user has all instant interval
		$allInstantSubscription = $model->allInstantSubscription($profile->id);

		$this->set('allInstantSubscription', $allInstantSubscription);
		$this->set('siteSubscribe', $siteSubscribe);
		$this->set('isSiteActive', $isSiteActive);
		$this->set('siteInterval', $siteInterval);
		$this->set('postSubscribe', $postSubscribe);
		$this->set('profile', $profile);
		$this->set('pagination', $pagination);
		$this->set('label', $label);
		$this->set('postDataSet', $postDataSet);
		$this->set('categoryDataSet', $categoryDataSet);
		$this->set('namespace', $namespace);
		$this->set('filter', $filter);

		parent::display('subscription/default');
	}
}
