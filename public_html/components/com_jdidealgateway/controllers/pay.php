<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

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
		// Create the view
		/** @var JdidealgatewayViewPay $view */
		$view = $this->getView('pay', 'html');

		// Add the export model
		/** @var JdidealgatewayModelPay $payModel */
		$payModel = $this->getModel('pay', 'JdidealgatewayModel');
		$view->setModel($payModel, true);

		// Set the layout
		$view->setLayout('ideal');

		// Display it all
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
		// Create the view
		/** @var JdidealgatewayViewPay $view */
		$view = $this->getView('pay', 'html');

		// Add the export model
		/** @var JdidealgatewayModelPay $payModel */
		$payModel = $this->getModel('pay', 'JdidealgatewayModel');
		$view->setModel($payModel, true);

		// Set the layout
		$view->setLayout('result');

		// Display it all
		$view->display();
	}
}
