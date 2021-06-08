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

use Jdideal\Gateway;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Status model
 *
 * @package  JDiDEAL
 * @since    4.13.0
 */
class JdidealgatewayModelStatus extends BaseDatabaseModel
{
	/**
	 * Get the message to show.
	 *
	 * @return  string  The message to show.
	 *
	 * @throws  Exception
	 *
	 * @since   4.13.0
	 */
	public function getMessage(): string
	{
		$menu = Factory::getApplication()->getMenu()->getActive();

		if (!$menu)
		{
			return '';
		}

		$profileId    = $menu->params->get('profile_id');
		$messageState = $menu->params->get('message');
		$gateway      = new Gateway;

		return $gateway->getMessage(0, $profileId, $messageState);
	}
}
