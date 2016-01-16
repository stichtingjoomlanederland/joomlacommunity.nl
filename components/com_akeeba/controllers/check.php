<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2014 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 2, or later
 *
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

defined('AKEEBA_BACKUP_ORIGIN') or define('AKEEBA_BACKUP_ORIGIN','frontend');

use Akeeba\Engine\Platform;

class AkeebaControllerCheck extends F0FController
{
	public function __construct($config = array())
    {
		$config['csrf_protection'] = false;

		parent::__construct($config);
	}

	public function execute($task)
    {
        // The only allowed task is the browse one
		$task = 'browse';
		parent::execute($task);
	}

	public function browse()
	{
		// Check permissions
		$this->_checkPermissions();

        /** @var AkeebaModelStatistics $model */
        $model = F0FModel::getTmpInstance('Statistics', 'AkeebaModel');
        $model->setInput($this->input);

        $result = $model->notifyFailed();

        $message  = $result['result'] ? '200 ' : '500 ';
        $message .= implode(', ', $result['message']);

        @ob_end_clean();
		header('Content-type: text/plain');
		header('Connection: close');
        echo $message;
        flush();
        JFactory::getApplication()->close();
	}


	/**
	 * Check that the user has sufficient permissions, or die in error
	 *
	 */
	private function _checkPermissions()
	{
		// Is frontend backup enabled?
		$febEnabled = Platform::getInstance()->get_platform_configuration_option('failure_frontend_enable', 0) != 0;

		// Is the Secret Key strong enough?
		$validKey = Platform::getInstance()->get_platform_configuration_option('frontend_secret_word', '');

		if (!\Akeeba\Engine\Util\Complexify::isStrongEnough($validKey, false))
		{
			$febEnabled = false;
		}

		if (!$febEnabled)
		{
			@ob_end_clean();
			echo '403 ' . JText::_('ERROR_NOT_ENABLED');
			flush();
			JFactory::getApplication()->close();
		}

		// Is the key good?
		$key = $this->input->get('key', '', 'none', 2);

		$validKeyTrim = trim($validKey);

		if (($key != $validKey) || (empty($validKeyTrim)))
		{
			@ob_end_clean();
			echo '403 ' . JText::_('ERROR_INVALID_KEY');
			flush();
			JFactory::getApplication()->close();
		}
	}
}