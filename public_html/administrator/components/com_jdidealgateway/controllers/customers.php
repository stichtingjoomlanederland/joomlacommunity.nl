<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\MVC\Controller\AdminController;

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
}
