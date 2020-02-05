<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') or die;

use FOF30\Model\Model;

class SchedulingInformation extends Model
{
	public function getPaths()
	{
		$ret = (object) array(
			'cli'      => (object) array(
				'supported' => false,
				'path'      => false
			),
			'altcli'   => (object) array(
				'supported' => false,
				'path'      => false
			),
			'frontend' => (object) array(
				'supported' => false,
				'path'      => false,
			),
			'info'     => (object) array(
				'windows'   => false,
				'php_path'  => false,
				'root_url'  => false,
				'secret'    => '',
				'feenabled' => false,
			)
		);

		// Get the absolute path to the site's root
		$absolute_root = rtrim(realpath(JPATH_ROOT), '/\\');
		// Is this Windows?
		$ret->info->windows = (DIRECTORY_SEPARATOR == '\\') || (substr(strtoupper(PHP_OS), 0, 3) == 'WIN');
		// Get the pseudo-path to PHP CLI
		if ($ret->info->windows)
		{
			$ret->info->php_path = 'c:\path\to\php.exe';
		}
		else
		{
			$ret->info->php_path = '/path/to/php';
		}
		// Get front-end backup secret key
		$ret->info->secret    = $this->container->params->get('frontend_secret_word', '');
		$ret->info->feenabled = $this->container->params->get('frontend_enable', false);
		// Get root URL
		$ret->info->root_url = rtrim($this->container->params->get('siteurl', ''), '/');

		// Get information for CLI CRON script
		$ret->cli->supported = true;
		$ret->cli->path      = $absolute_root . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'admintools-filescanner.php';

		// Get information for alternative CLI CRON script
		$ret->altcli->supported = true;

		if (trim($ret->info->secret) && $ret->info->feenabled)
		{
			$ret->altcli->path = $absolute_root . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'admintools-filescanner-alt.php';
		}

		// Get information for front-end backup
		$ret->frontend->supported = true;
		if (trim($ret->info->secret) && $ret->info->feenabled)
		{
			$ret->frontend->path = 'index.php?option=com_admintools&view=FileScanner&key='
				. urlencode($ret->info->secret);
		}

		return $ret;
	}
}
