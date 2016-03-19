<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 *
 * @since     1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class AkeebaViewTransfer extends F0FViewHtml
{
	/** @var   array|null  Latest backup information */
	public $latestBackup = [];

	/** @var   string  Date of the latest backup, human readable */
	public $lastBackupDate = '';

	/** @var   array  Space required on the target server */
	public $spaceRequired = [
		'size'   => 0,
		'string' => '0.00 Kb'
	];

	/** @var   string  The URL to the site we are restoring to (from the session) */
	public $newSiteUrl = '';

	/** @var   array  Results of support and firewall status of the known file transfer methods */
	public $ftpSupport = [
		'supported'	=> [
			'ftp'	=> false,
			'ftps'	=> false,
			'sftp'	=> false,
		],
		'firewalled'	=> [
			'ftp'	=> false,
			'ftps'	=> false,
			'sftp'	=> false
		]
	];

	/** @var   array  Available transfer options, for use by JHTML */
	public $transferOptions = [];

	/** @var   bool  Do I have supported but firewalled methods? */
	public $hasFirewalledMethods = false;

	/** @var   string  Currently selected transfer option */
	public $transferOption = 'manual';

	/** @var   string  FTP/SFTP host name */
	public $ftpHost = '';

	/** @var   string  FTP/SFTP port (empty for default port) */
	public $ftpPort = '';

	/** @var   string  FTP/SFTP username */
	public $ftpUsername = '';

	/** @var   string  FTP/SFTP password – or certificate password if you're using SFTP with SSL certificates */
	public $ftpPassword = '';

	/** @var   string  SFTP public key certificate path */
	public $ftpPubKey = '';

	/** @var   string  SFTP private key certificate path */
	public $ftpPrivateKey = '';

	/** @var   string  FTP/SFTP directory to the new site's root */
	public $ftpDirectory = '';

	/** @var   string  FTP passive mode (default is true) */
	public $ftpPassive = true;

	/**
	 * Runs on the wizard (default) task
	 *
	 * @param   string|null  $tpl  Ignored
	 *
	 * @return  bool  True to let the view display
	 */
	public function onWizard($tpl = null)
	{
		AkeebaStrapper::addJSfile('media://com_akeeba/js/transfer.js');

		/** @var AkeebaModelTransfers $model */
		$model                  = $this->getModel();
		$session			    = JFactory::getSession();

		$this->latestBackup     = $model->getLatestBackupInformation();
		$this->spaceRequired    = $model->getApproximateSpaceRequired();
		$this->newSiteUrl       = $session->get('transfer.url', '', 'akeeba');
		$this->newSiteUrlResult = $session->get('transfer.url_status', '', 'akeeba');
		$this->ftpSupport	    = $session->get('transfer.ftpsupport', null, 'akeeba');
		$this->transferOption   = $session->get('transfer.transferOption', null, 'akeeba');
		$this->ftpHost          = $session->get('transfer.ftpHost', null, 'akeeba');
		$this->ftpPort          = $session->get('transfer.ftpPort', null, 'akeeba');
		$this->ftpUsername      = $session->get('transfer.ftpUsername', null, 'akeeba');
		$this->ftpPassword      = $session->get('transfer.ftpPassword', null, 'akeeba');
		$this->ftpPubKey        = $session->get('transfer.ftpPubKey', null, 'akeeba');
		$this->ftpPrivateKey    = $session->get('transfer.ftpPrivateKey', null, 'akeeba');
		$this->ftpDirectory     = $session->get('transfer.ftpDirectory', null, 'akeeba');
		$this->ftpPassive       = $session->get('transfer.ftpPassive', 1, 'akeeba');

		if (!empty($this->latestBackup))
		{
			$lastBackupDate = JFactory::getDate($this->latestBackup['backupstart'], 'UTC');
			$this->lastBackupDate = $lastBackupDate->format(JText::_('DATE_FORMAT_LC'), true);

			$session->set('transfer.lastBackup', $this->latestBackup, 'akeeba');
		}

		if (empty($this->ftpSupport))
		{
			$this->ftpSupport = $model->getFTPSupport();
			$session->set('transfer.ftpsupport', $this->ftpSupport, 'akeeba');
		}

		$this->transferOptions  = $this->getTransferMethodOptions();

		/*
		foreach ($this->ftpSupport['firewalled'] as $method => $isFirewalled)
		{
			if ($isFirewalled && $this->ftpSupport['supported'][$method])
			{
				$this->hasFirewalledMethods = true;

				break;
			}
		}
		*/

		return true;
	}

	/**
	 * Returns the JHTML options for a transfer methods drop-down, filtering out the unsupported and firewalled methods
	 *
	 * @return   array
	 */
	private function getTransferMethodOptions()
	{
		$options = [];

		foreach ($this->ftpSupport['supported'] as $method => $supported)
		{
			if (!$supported)
			{
				continue;
			}

			$methodName = JText::_('COM_AKEEBA_TRANSFER_LBL_TRANSFERMETHOD_' . $method);

			if ($this->ftpSupport['firewalled'][$method])
			{
				$methodName = '&#128274; ' . $methodName;
			}

			$options[] = JHtml::_('select.option', $method, $methodName);
		}

		$options[] = JHtml::_('select.option', 'manual', JText::_('COM_AKEEBA_TRANSFER_LBL_TRANSFERMETHOD_MANUALLY'));

		return $options;
	}
}