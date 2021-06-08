<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

/**
 * Subscriptions controller.
 *
 * @package  JDiDEAL
 * @since    6.4.0
 */
class JdidealgatewayControllerSubscriptions extends BaseController
{
	/**
	 * Handle the cancellation of a subscription.
	 *
	 * @return  void
	 *
	 * @since   6.4.0
	 */
	public function cancel(): void
	{
		$this->checkToken();

		$type    = '';
		$message = Text::_('COM_ROPAYMENTS_SUBSCRIPTION_HAS_BEEN_CANCELLED');

		try
		{
			$subscriptionId = $this->input->get('subscriptionId');

			require_once JPATH_ADMINISTRATOR . '/components/com_jdidealgateway/models/subscription.php';

			/** @var JdidealgatewayModelSubscription $model */
			$model = new JdidealgatewayModelSubscription(['ignore_request' => true]);
			$model->cancelSubscription($subscriptionId);
		}
		catch (Exception $exception)
		{
			$type    = 'error';
			$message = $exception->getMessage();
		}

		$this->setRedirect(
			Route::_('index.php?option=com_jdidealgateway&view=subscription'), $message, $type
		);
		$this->redirect();
	}
}
