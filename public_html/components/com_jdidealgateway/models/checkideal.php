<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Model for handling the payment.
 *
 * @package  JDiDEAL
 * @since    2.0
 */
class JdidealgatewayModelCheckideal extends JModelLegacy
{
	/**
	 * Load the transaction details.
	 *
	 * @param   int  $logid  The log ID to get the details for.
	 *
	 * @return  object  The object with payment details.
	 *
	 * @since   2.0
	 */
	public function loadDetails($logid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__jdidealgateway_logs'))
			->where($db->quoteName('id') . ' = ' . (int) $logid);
		$db->setQuery($query);

		return  $db->loadObject();
	}
}
