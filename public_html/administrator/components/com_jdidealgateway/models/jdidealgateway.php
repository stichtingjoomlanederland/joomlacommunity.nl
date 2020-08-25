<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

/**
 * RO Payments model.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealgatewayModelJdidealgateway extends BaseDatabaseModel
{
	/**
	 * Check if the notify script can be reached.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   4.4.0
	 */
	public function checkSystemRequirements(): void
	{
		$db = $this->getDbo();

		// Check if we have an alias
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__jdidealgateway_profiles'));
		$db->setQuery($query, 0, 1);

		$id = $db->loadResult();

		if (!$id)
		{
			// Show message of missing profile alias
			Factory::getApplication()->enqueueMessage(Text::_('COM_ROPAYMENTS_NO_PROFILE_FOUND'), 'warning');

			return;
		}

		// Check if the notify.php is available, only when we have an alias
		$http = HttpFactory::getHttp(null, array('curl', 'stream'));
		$url  = Uri::root() . 'cli/notify.php';
		$app  = Factory::getApplication();

		try
		{
			$response = $http->get($url);

			if ($response->code !== 200)
			{
				$app->enqueueMessage(
					Text::sprintf('COM_ROPAYMENTS_NOTIFY_NOT_AVAILABLE', $url, $url, $response->code, $response->body),
					'error'
				);
			}
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		// Check if cURL is active
		if (!function_exists('curl_init') || !is_callable('curl_init'))
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_ROPAYMENTS_CURL_NOT_AVAILABLE'), 'error');
		}
	}
}
