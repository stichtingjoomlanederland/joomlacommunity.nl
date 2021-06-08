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

use Joomla\CMS\Authentication\Authentication;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

/**
 * RO Payments Authentication plugin
 *
 * @since  6.4.0
 */
class PlgAuthenticationRopayments extends CMSPlugin
{
	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array    $credentials  Array holding the user credentials
	 * @param   array    $options      Array of extra options
	 * @param   object  &$response     Authentication response object
	 *
	 * @return  void
	 *
	 * @since   6.4.0
	 */
	public function onUserAuthenticate($credentials, $options, &$response)
	{
		if (!isset($options['source'], $options['entry_url'], $credentials['username'])
			|| $options['source'] !== 'RO Payments'
		)
		{
			return;
		}

		$uri = Uri::getInstance($options['entry_url']);

		if (!$uri->hasVar('transactionId') || !$uri->hasVar('pid'))
		{
			return;
		}

		$response->type          = 'RO Payments';
		$response->status        = Authentication::STATUS_SUCCESS;
		$response->error_message = '';
	}
}
