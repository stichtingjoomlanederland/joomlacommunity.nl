<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport( 'joomla.mail.helper' );

class EasyDiscussControllerSubscription extends EasyDiscussController
{
	/**
	 * Processes user subscription.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	null
	 */
	public function subscribe()
	{
		ED::checktoken();

		// Get variables from post.
		$type = $this->input->get('type', null);
		$name = $this->input->get('subscribe_name', '', 'string');
		$email = $this->input->get('subscribe_email', '', 'string');
		// $interval = $this->input->get('subscription_interval' ,'weekly', 'string');
		$cid = $this->input->get('cid', 0, 'int');
		$redirect = $this->input->get('redirect', '');

		// default interval to weekly for site / cat, post to daily
		// if the email digest is disabled, make a 'instant' as a default interval
		$interval = ($type == 'post' || !$this->config->get('main_email_digest')) ? 'instant' :'weekly';

		if (empty($redirect)) {
			$redirect = EDR::_('index.php?option=com_easydiscuss', false);

			if ($type == 'category' && $cid) {
				$redirect = EDR::getCategoryRoute($cid, false);
			}
		} else {
			$redirect = base64_decode($url);
		}

		// Apply filtering on the name.
		$filter = JFilterInput::getInstance();
		$name = $filter->clean($name, 'STRING');
		$email = JString::trim($email);
		$name = JString::trim($name);

		if (!JMailHelper::isEmailAddress($email)) {
			ED::setMessageQueue(JText::_('COM_EASYDISCUSS_INVALID_EMAIL'), 'error');
			$this->app->redirect($redirect);
			$this->app->close();
		}

		// Check for empty email
		if (empty($email)) {
			ED::setMessageQueue(JText::_('COM_EASYDISCUSS_EMAIL_IS_EMPTY'), 'error');
			$this->app->redirect($redirect);
			$this->app->close();
		}

		// Check for empty name
		if (empty($name)) {
			ED::setMessageQueue(JText::_('COM_EASYDISCUSS_NAME_IS_EMPTY'), 'error');
			$this->app->redirect($redirect);
			$this->app->close();
		}

		$model = ED::model('Subscribe');
		$subscription = $model->isSiteSubscribed($type, $email, $cid);

		$data = array();
		$data['type'] = $type;
		$data['userid'] = $this->my->id;
		$data['email'] = $email;
		$data['cid'] = $cid;
		$data['member'] = ($this->my->id)? true:false;
		$data['name'] = ($this->my->id)? $this->my->name : $name;
		$data['interval'] = $interval;


		if ($subscription) {
			// Perhaps the user tried to change the subscription interval.
			if ($subscription->interval == $interval) {
				ED::setMessageQueue(JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UPDATED_SUCCESSFULLY'), 'success');
				$this->app->redirect($redirect);
				return $this->app->close();
			}

			// User changed their subscription interval.
			if (!$model->updateSiteSubscription($subscription->id, $data)) {
				//if($model->updateSiteSubscription($subRecord['id'], $subscription_info))
				ED::setMessageQueue(JText::_('COM_EASYDISCUSS_SUBSCRIPTION_FAILED'), 'error');
				$app->redirect($redirect);
				return $app->close();
			}

			// If the user already has an existing subscription, just let them know that their subscription is already updated.
			$intervalMessage = JText::_('COM_EASYDISCUSS_SUBSCRIPTION_INTERVAL_' . strtoupper($interval));

			ED::setMessageQueue(JText::sprintf('COM_EASYDISCUSS_SUBSCRIPTION_UPDATED', $intervalMessage), 'success');
			$this->app->redirect($redirect);
			return $this->app->close();
		}

		// If there is no subscription record for this user, add it here
		if (!$model->addSubscription($data)) {
			ED::setMessageQueue(JText::_('COM_EASYDISCUSS_SUBSCRIPTION_FAILED' ), 'error');
			$this->app->redirect($redirect);
			return $this->app->close();
		}

		ED::setMessageQueue(JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UPDATED_SUCCESSFULLY'), 'success');
		$this->app->redirect($redirect);
		return $this->app->close();
	}

	function unsubscribe()
	{
		$my = JFactory::getUser();

		$redirectLInk = 'index.php?option=com_easydiscuss&view=subscription';

		if ($my->id == 0) {
			$redirectLInk = 'index.php?option=com_easydiscuss&view=index';
		}

		$data = base64_decode(JRequest::getVar('data', ''));

		$param = ED::registry($data);
		$param->type = $param->get('type', '');
		$param->sid = $param->get('sid', '');
		$param->uid = $param->get('uid', '');
		$param->token = $param->get('token', '');

		$subtable = ED::table('Subscribe');
		$subtable->load($param->sid);

		$token = md5($subtable->id.$subtable->created);
		$paramToken = md5($param->sid.$subtable->created);

		if (empty($subtable->id)) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_SUBSCRIPTION_NOT_FOUND'), 'error');
			$this->setRedirect(EDR::_($redirectLInk, false));
			return false;
		}

		if ($token != $paramToken) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_FAILED'), 'error');
			$this->setRedirect(EDR::_($redirectLInk, false));
			return false;
		}

		if (!$subtable->delete($param->sid)) {
			ED::setMessage( JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_FAILED_ERROR_DELETING_RECORDS'), 'error');
			$this->setRedirect(EDR::_($redirectLInk, false));
			return false;
		}


		ED::setMessage(JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_SUCCESS'));
		$this->setRedirect(EDR::_($redirectLInk, false));
		return true;
	}
}
