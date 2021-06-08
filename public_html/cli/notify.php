<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Jdideal\Gateway;
use Jdideal\Status\Request;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\Session\SessionInterface;

define('_JEXEC', 1);

if (file_exists((dirname(__DIR__)) . '/defines.php'))
{
	require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}

if (file_exists(JPATH_LIBRARIES . '/import.legacy.php'))
{
	require_once JPATH_LIBRARIES . '/import.legacy.php';
}
elseif (file_exists(JPATH_LIBRARIES . '/import.php'))
{
	require_once JPATH_LIBRARIES . '/import.php';
}

require_once JPATH_BASE . '/includes/framework.php';

if (version_compare(JVERSION, '3.8.5', '<'))
{
	require_once JPATH_LIBRARIES . '/import.php';
}

require_once JPATH_CONFIGURATION . '/configuration.php';

if (JVERSION < 4)
{
	$app      = Factory::getApplication('site');
	$language = Factory::getLanguage();
}
else
{
	$_SERVER['argv'] = [];
	$container = Factory::getContainer();
	$container->alias('session', 'session.web.site')
		->alias('JSession', 'session.web.site')
		->alias(Session::class, 'session.web.site')
		->alias(\Joomla\Session\Session::class, 'session.web.site')
		->alias(SessionInterface::class, 'session.web.site');

	try
	{
		$app = $container->get(SiteApplication::class);
	}
	catch (Exception $exception)
	{
		echo $exception->getMessage();
		exit();
	}

	$config   = $container->get('config');
	$locale   = $config->get('language');
	$debug    = $config->get('debug_lang');
	$language = $container->get(LanguageFactoryInterface::class)
		->createLanguage($locale, $debug);
}

$language->load('com_jdidealgateway', JPATH_SITE . '/components/com_jdidealgateway/', 'en-GB', true);
$language->load('com_jdidealgateway', JPATH_SITE . '/components/com_jdidealgateway/', $language->getDefault(), true);
$language->load('com_jdidealgateway', JPATH_SITE . '/components/com_jdidealgateway/', null, true);

if (!isset($_SERVER['HTTP_HOST']))
{
	$_SERVER['HTTP_HOST'] = ComponentHelper::getParams('com_jdidealgateway')->get('domain');
}

JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES);

if (class_exists(Gateway::class) === false)
{
	JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES . '/Jdideal');
}

$statusRequest = new Request;

try
{
	$result = $statusRequest->batch();

	if ($result['isCustomer'])
	{
		$app->enqueueMessage($result['message'], $result['level']);
		$app->redirect($result['url']);
	}
	else
	{
		echo $result['status'];
	}
}
catch (Exception $exception)
{
	// Write the error log
	$statusRequest->writeErrorLog($exception->getMessage());

	try
	{
		$customer = $statusRequest->whoIsCalling();

		if ($customer)
		{
			$app->enqueueMessage($exception->getMessage(), 'error');
			$app->redirect('/');
		}
		else
		{
			echo 'NOK';
		}
	}
	catch (Exception $exception)
	{
		// Cannot determine if customer or PSP is calling, just show the message
		echo $exception->getMessage();
	}
}
