<?php
/**
 * @package    RO Payments
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;

/**
 * RO Payments Emails controller.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealgatewayControllerEmails extends AdminController
{
	protected $text_prefix = 'COM_ROPAYMENTS_EMAILS';

	/**
	 * Send a test email.
	 *
	 * @return  void
	 *
	 * @since   2.8.2
	 * @throws  Exception
	 */
	public function testEmail(): void
	{
		Session::checkToken() or die();

		$id    = $this->input->getInt('emailId', 0);
		$email = $this->input->get('email', null, '');

		/** @var JdidealgatewayModelEmail $model */
		$model  = $this->getModel('Email', 'JdidealgatewayModel');
		$result = $model->testEmail($id, $email);
		$app    = Factory::getApplication();
		$app->enqueueMessage($result['msg'], $result['state']);
		$app->redirect('index.php?option=com_jdidealgateway&view=emails');
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \JModelLegacy|boolean  Model object on success; otherwise false on failure.
	 *
	 * @since   5.0.0
	 */
	public function getModel($name = 'Email', $prefix = 'JdidealgatewayModel',
		$config = []
	) {
		return parent::getModel($name, $prefix, $config);
	}
}
