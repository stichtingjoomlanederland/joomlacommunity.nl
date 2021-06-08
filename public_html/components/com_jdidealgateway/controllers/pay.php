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

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Payment page controller.
 *
 * @package  JDiDEAL
 * @since    3.0
 */
class JdidealgatewayControllerPay extends BaseController
{
	/**
	 * Show the iDEAL payment page.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 * @throws  Exception
	 */
	public function sendmoney(): void
	{
		/** @var JdidealgatewayViewPay $view */
		$view = $this->getView('pay', 'html');

		/** @var JdidealgatewayModelPay $payModel */
		$payModel = $this->getModel('pay', 'JdidealgatewayModel');
		$view->setModel($payModel, true);

		$view->setLayout('ideal');

		$view->display();
	}

	/**
	 * Check the payment result.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 * @throws  Exception
	 */
	public function result(): void
	{
		/** @var JdidealgatewayViewPay $view */
		$view = $this->getView('pay', 'html');

		/** @var JdidealgatewayModelPay $payModel */
		$payModel = $this->getModel('pay', 'JdidealgatewayModel');

		$trans  = $this->input->getString('transactionId');
		$column = 'trans';

		if (empty($trans))
		{
			$trans  = $this->input->getString('pid');
			$column = 'pid';
		}

		$view->set('result', $payModel->getResult($trans, $column));
		$view->setLayout('result');

		$view->display();
	}
}
