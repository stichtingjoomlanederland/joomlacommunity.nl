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
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

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
	 * @since   4.4.0
	 * @throws  Exception
	 */
	public function checkSystemRequirements(): void
	{
		$this->checkAliasExists();
		$this->checkNotifyScript();
		$this->checkCurlAvailable();
	}

	/**
	 * Check if there is an alias.
	 *
	 * @return  void
	 *
	 * @since   6.2.0
	 * @throws  Exception
	 */
	private function checkAliasExists(): void
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
			Factory::getApplication()->enqueueMessage(
				Text::_('COM_ROPAYMENTS_NO_PROFILE_FOUND'), 'warning'
			);
		}
	}

	/**
	 * Check if the notify.php is available, only when we have an alias.
	 *
	 * @return  void
	 *
	 * @since   6.2.0
	 * @throws  Exception
	 */
	private function checkNotifyScript(): void
	{
		$app = Factory::getApplication();

		try
		{
			$options    = new Registry;
			$http       = HttpFactory::getHttp($options, ['curl', 'stream']);
			$url        = Uri::root() . 'cli/notify.php';
			$response   = $http->get($url);
			$statusCode = JVERSION < 4 ? $response->code
				: $response->getStatusCode();

			if ($statusCode !== 200)
			{
				$reason = JVERSION < 4 ? $response->body
					: $response->getReasonPhrase();
				$app->enqueueMessage(
					Text::sprintf(
						'COM_ROPAYMENTS_NOTIFY_NOT_AVAILABLE', $url, $url,
						$statusCode, $reason
					),
					'error'
				);
			}
		}
		catch (Exception $exception)
		{
			$app->enqueueMessage($exception->getMessage(), 'error');
		}
	}

	/**
	 * Check if cURL is active.
	 *
	 * @return  void
	 *
	 * @since   6.2.0
	 * @throws  Exception
	 */
	private function checkCurlAvailable(): void
	{
		if (!function_exists('curl_init') || !is_callable('curl_init'))
		{
			Factory::getApplication()->enqueueMessage(
				Text::_('COM_ROPAYMENTS_CURL_NOT_AVAILABLE'), 'error'
			);
		}
	}
}
