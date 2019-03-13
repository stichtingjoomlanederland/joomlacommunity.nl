<?php
/**
 * Akeeba Engine
 * The PHP-only site backup engine
 *
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Postproc;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Postproc\Connector\Azure as AzureConnector;
use Akeeba\Engine\Postproc\Connector\Azure\AzureStorage as AzureStorage;
use Akeeba\Engine\Postproc\Connector\Azure\Retrypolicy\None as AzureRetryNone;

class Azure extends Base
{
	public function __construct()
	{
		$this->can_delete = true;
	}

	public function processPart($absolute_filename, $upload_as = null)
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$account 	= trim($config->get('engine.postproc.azure.account', ''));
		$key 		= trim($config->get('engine.postproc.azure.key', ''));
		$container 	= $config->get('engine.postproc.azure.container', 0);
		$useSSL		= $config->get('engine.postproc.azure.usessl', 1);
		$directory 	= $config->get('volatile.postproc.directory', null);

		if (empty($directory))
		{
			$directory = $config->get('engine.postproc.azure.directory', 0);
		}

		// Sanity checks
		if (empty($account))
		{
			$this->setWarning('You have not set up your Windows Azure account name');

			return false;
		}

		if (empty($key))
		{
			$this->setWarning('You have not set up your Windows Azure key');

			return false;
		}

		if (empty($container))
		{
			$this->setWarning('You have not set up your Windows Azure container');

			return false;
		}

		// Fix the directory name, if required
		if (!empty($directory))
		{
			$directory = trim($directory);
			$directory = ltrim(Factory::getFilesystemTools()->TranslateWinPath($directory), '/');
		}
		else
		{
			$directory = '';
		}

		// Parse tags
		$directory = Factory::getFilesystemTools()->replace_archive_name_variables($directory);
		$config->set('volatile.postproc.directory', $directory);

		// Calculate relative remote filename
		$filename = basename($absolute_filename);

		if (!empty($directory) && ($directory != '/'))
		{
			$filename = $directory . '/' . $filename;
		}

		// Store the absolute remote path in the class property
		$this->remote_path = $filename;

		// Connect and send
		try
		{
			$blob = new AzureConnector(AzureStorage::URL_CLOUD_BLOB, $account, $key);
			$policyNone = new AzureRetryNone();
			$blob->setRetryPolicy($policyNone);
			$blob->putBlob($container, $filename, $absolute_filename);
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return true;
	}

	public function delete($path)
	{
		$config = Factory::getConfiguration();

		$account = trim($config->get('engine.postproc.azure.account', ''));
		$key = trim($config->get('engine.postproc.azure.key', ''));
		$container = $config->get('engine.postproc.azure.container', 0);

		// Sanity checks
		if (empty($account))
		{
			$this->setWarning('You have not set up your Windows Azure account name');

			return false;
		}

		if (empty($key))
		{
			$this->setWarning('You have not set up your Windows Azure key');

			return false;
		}

		if (empty($container))
		{
			$this->setWarning('You have not set up your Windows Azure container');

			return false;
		}

		// Actually delete the BLOB
		try
		{
			$blob = new AzureConnector(AzureStorage::URL_CLOUD_BLOB, $account, $key);
			$policyNone = new AzureRetryNone();
			$blob->setRetryPolicy($policyNone);
			$blob->deleteBlob($container, $path);
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return true;
	}
}
