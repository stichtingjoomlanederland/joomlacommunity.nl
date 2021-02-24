<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die;

/**
 * Customers controller.
 *
 * @package  JDiDEAL
 * @since    5.0.0
 */
class JdidealgatewayControllerCustomers extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  6.1.0
	 */
	protected $text_prefix = 'COM_ROPAYMENTS_CUSTOMER';

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name.
	 * @param   string  $prefix  The model prefix.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JdidealgatewayModelCustomer  An instance of the JdidealgatewayModelCustomer class.
	 *
	 * @since   5.0.0
	 */
	public function getModel($name = 'Customer', $prefix = 'JdidealgatewayModel', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	/**
	 * Sync the subscriptions.
	 *
	 * @return  void
	 *
	 * @since   6.3.0
	 */
	public function sync(): void
	{
		Session::checkToken() or die();

		$message = Text::_('COM_ROPAYMENTS_CUSTOMERS_SYNCED_OK');
		$type    = '';

		try
		{
			$model = $this->getModel();
			$model->sync();
		}
		catch (Exception $exception)
		{
			$message = $exception->getMessage();
			$type    = 'error';
		}

		$this->setRedirect('index.php?option=com_jdidealgateway&view=customers', $message, $type);
		$this->redirect();
	}
}
