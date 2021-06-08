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
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;

/**
 * Subscriptions controller.
 *
 * @package  JDiDEAL
 * @since    5.0.0
 */
class JdidealgatewayControllerSubscriptions extends AdminController
{
	/**
	 * Text prefix
	 *
	 * @var    string
	 * @since  6.0.1
	 */
	public $text_prefix = 'COM_ROPAYMENTS_SUBSCRIPTIONS';

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name.
	 * @param   string  $prefix  The model prefix.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JdidealgatewayModelSubscription  An instance of the JdidealgatewayModelSubscription class.
	 *
	 * @since   5.0.0
	 */
	public function getModel($name = 'Subscription', $prefix = 'JdidealgatewayModel', $config = [])
	{
		return parent::getModel($name, $prefix, ['ignore_request' => true]);
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

		$message = Text::_('COM_ROPAYMENTS_SUBSCRIPTIONS_SYNCED_OK');
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

		$this->setRedirect('index.php?option=com_jdidealgateway&view=subscriptions', $message, $type);
		$this->redirect();
	}
}
