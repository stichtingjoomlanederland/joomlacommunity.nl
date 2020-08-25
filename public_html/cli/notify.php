<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Jdideal\Status\Request;
use Joomla\CMS\Factory;

define('_JEXEC', 1);

// Setup the path related constants.
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(__DIR__));
define('JPATH_ROOT', JPATH_BASE);
define('JPATH_SITE', JPATH_ROOT);
define('JPATH_CONFIGURATION', JPATH_ROOT);
define('JPATH_ADMINISTRATOR', JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator');
define('JPATH_LIBRARIES', JPATH_ROOT . DIRECTORY_SEPARATOR . 'libraries');
define('JPATH_PLUGINS', JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins');
define('JPATH_INSTALLATION', JPATH_ROOT . DIRECTORY_SEPARATOR . 'installation');
define('JPATH_THEMES', JPATH_BASE . DIRECTORY_SEPARATOR . 'templates');
define('JPATH_CACHE', JPATH_BASE . DIRECTORY_SEPARATOR . 'cache');
define('JPATH_MANIFESTS', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'manifests');
define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components/com_jdidealgateway');

// Load the library importer.
require_once JPATH_BASE . '/includes/framework.php';

// Only load this file in Joomla lower than version 3.8.5
if (version_compare(JVERSION, '3.8.5', '<'))
{
	require_once JPATH_LIBRARIES . '/import.php';
}

require_once JPATH_CONFIGURATION . '/configuration.php';

// Create the Application
$app   = Factory::getApplication('site');
$input = Factory::getApplication()->input;

// Load the language files
$language = Factory::getLanguage();
$language->load('com_jdidealgateway', JPATH_SITE . '/components/com_jdidealgateway/', 'en-GB', true);
$language->load('com_jdidealgateway', JPATH_SITE . '/components/com_jdidealgateway/', $language->getDefault(), true);
$language->load('com_jdidealgateway', JPATH_SITE . '/components/com_jdidealgateway/', null, true);

$_SERVER['HTTP_HOST'] = 'example.com';

// Setup the autoloader
JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES);

// Load RO Payments
$statusRequest = new Request;

try
{
	$result = $statusRequest->batch();

	if ($result['isCustomer'])
	{
		$app->redirect($result['url'], $result['message'], $result['level']);
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
			$app->redirect('/', $exception->getMessage(), 'error');
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
