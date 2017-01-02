<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') or die;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use FOF30\Container\Container;
use FOF30\Input\Input;
use FOF30\Model\Model;

class Scanner extends Model
{
	private $aeconfig;

	public function __construct(Container $container, $config = array())
	{
		parent::__construct($container, $config);

		// Load the Akeeba Engine autoloader
		define('AKEEBAENGINE', 1);
		require_once JPATH_ADMINISTRATOR . '/components/com_admintools/engine/Autoloader.php';

		// Load the platform
		Platform::addPlatform('filescan', JPATH_ADMINISTRATOR . '/components/com_admintools/platform/Filescan');

		// Load the engine configuration
		Platform::getInstance()->load_configuration(1);

		$this->aeconfig = Factory::getConfiguration();
	}

	private function getTextInputAsArray($input, $trim = '')
	{
		$result = array();

		foreach (explode("\n", $input) as $entry)
		{
			$entry = preg_replace('/\s+/', '', $entry);
			if (!empty($trim))
			{
				$entry = trim($entry, '/');
			}
			if (!empty($entry))
			{
				$result[] = $entry;
			}
		}

		return $result;
	}

	public function saveConfiguration()
	{
		$rawInput = $this->getState('rawinput', array());

		$input = new Input($rawInput);

		$newFileExtension  = trim($input->getString('fileextensions', ''));
		$newExcludeFolders = trim($input->getString('exludefolders', ''));
		$newExcludeFiles   = trim($input->getString('exludefiles', ''));
		$newMinExecTime    = trim($input->getInt('mintime', ''));
		$newMaxExecTime    = trim($input->getInt('maxtime', ''));
		$newRuntimeBias    = trim($input->getInt('runtimebias', ''));

		$protectedKeys = $this->aeconfig->getProtectedKeys();
		$this->aeconfig->resetProtectedKeys();

		$this->aeconfig->set('akeeba.basic.file_extensions', implode('|',
			$this->getTextInputAsArray(
				$newFileExtension
			)));
		$this->aeconfig->set('akeeba.basic.exclude_folders', implode('|',
			$this->getTextInputAsArray(
				$newExcludeFolders, '/'
			)));
		$this->aeconfig->set('akeeba.basic.exclude_files', implode('|',
			$this->getTextInputAsArray(
				$newExcludeFiles, '/'
			)));
		$this->aeconfig->set('akeeba.tuning.min_exec_time', $newMinExecTime);
		$this->aeconfig->set('akeeba.tuning.max_exec_time', $newMaxExecTime);
		$this->aeconfig->set('akeeba.tuning.run_time_bias', $newRuntimeBias);

		Platform::getInstance()->save_configuration();

		$this->aeconfig->setProtectedKeys($protectedKeys);
	}

	public function getFileExtensions()
	{
		return explode('|', $this->aeconfig->get('akeeba.basic.file_extensions', 'php|phps|php3|inc'));
	}

	public function getExcludeFolders()
	{
		return explode('|', $this->aeconfig->get('akeeba.basic.exclude_folders', ''));
	}

	public function getExcludeFiles()
	{
		return explode('|', $this->aeconfig->get('akeeba.basic.exclude_files', ''));
	}

	public function getMinExecTime()
	{
		return $this->aeconfig->get('akeeba.tuning.min_exec_time', 1000);
	}

	public function getMaxExecTime()
	{
		return $this->aeconfig->get('akeeba.tuning.max_exec_time', 5);
	}

	public function getRuntimeBias()
	{
		return $this->aeconfig->get('akeeba.tuning.run_time_bias', 75);
	}
}