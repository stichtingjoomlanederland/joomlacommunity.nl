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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Handle the payments.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealgatewayModelCheckout extends BaseDatabaseModel
{
	/**
	 * Retrieve the payment data from the session.
	 *
	 * @return  array  Data used in the ExtraPayment form.
	 *
	 * @since   2.0.0
	 * @throws  Exception
	 */
	public function getVariables(): array
	{
		return json_decode(base64_decode(Factory::getApplication()->input->post->getBase64('vars', '')), true);
	}
}
