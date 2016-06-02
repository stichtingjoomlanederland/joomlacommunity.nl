<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\Controller;

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Backup\Admin\Controller\Mixin\CustomACL;
use Akeeba\Backup\Admin\Controller\Mixin\PredefinedTaskList;
use Exception;
use FOF30\Container\Container;
use FOF30\Controller\Controller;

/**
 * Controller for the Site Transfer Wizard
 */
class Transfer extends Controller
{
	use CustomACL, PredefinedTaskList;

	public function __construct(Container $container, array $config)
	{
		parent::__construct($container, $config);

		$this->setPredefinedTaskList(['wizard', 'checkUrl', 'applyConnection', 'initialiseUpload', 'upload', 'reset']);
	}

	/**
	 * Reset the wizard
	 *
	 * @return  void
	 */
	public function reset()
	{
		$session = $this->container->session;
		$session->set('transfer', null, 'akeeba');
		$session->set('transfer.url', null, 'akeeba');
		$session->set('transfer.url_status', null, 'akeeba');
		$session->set('transfer.ftpsupport', null, 'akeeba');

		/** @var \Akeeba\Backup\Admin\Model\Transfer $model */
		$model = $this->getModel();
		$model->resetUpload();

		$this->setRedirect('index.php?option=com_akeeba&view=Transfer');
	}

	/**
	 * Cleans and checks the validity of the new site's URL
	 *
	 * @return  void
	 */
	public function checkUrl()
	{
		$url = $this->input->get('url', '', 'raw');

		/** @var \Akeeba\Backup\Admin\Model\Transfer $model */
		$model = $this->getModel();
		$model->savestate(true);
		$result = $model->checkAndCleanUrl($url);

		$session = $this->container->session;
		$session->set('transfer.url', $result['url'], 'akeeba');
		$session->set('transfer.url_status', $result['status'], 'akeeba');

		@ob_end_clean();
		echo '###' . json_encode($result) . '###';
		$this->container->platform->closeApplication();
	}

	/**
	 * Applies the FTP/SFTP connection information and makes some preliminary validation
	 *
	 * @return  void
	 */
	public function applyConnection()
	{
		$result = (object)[
			'status'    => true,
			'message'   => '',
		];

		// Get the parameters from the request
		$transferOption = $this->input->getCmd('method', 'ftp');
		$ftpHost = $this->input->get('host', '', 'raw', 2);
		$ftpPort = $this->input->getInt('port', null);
		$ftpUsername = $this->input->get('username', '', 'raw', 2);
		$ftpPassword = $this->input->get('password', '', 'raw', 2);
		$ftpPubKey = $this->input->get('public', '', 'raw', 2);
		$ftpPrivateKey = $this->input->get('private', '', 'raw', 2);
		$ftpPassive = $this->input->getInt('passive', 1);
		$ftpDirectory = $this->input->get('directory', '', 'raw', 2);

		// Fix the port if it's missing
		if (empty($ftpPort))
		{
			switch ($transferOption)
			{
				case 'ftp':
					$ftpPort = 21;
					break;

				case 'ftps':
					$ftpPort = 990;
					break;

				case 'sftp':
					$ftpPort = 22;
					break;
			}
		}

		// Store everything in the session
		$session = $this->container->session;

		$session->set('transfer.transferOption', $transferOption, 'akeeba');
		$session->set('transfer.ftpHost', $ftpHost, 'akeeba');
		$session->set('transfer.ftpPort', $ftpPort, 'akeeba');
		$session->set('transfer.ftpUsername', $ftpUsername, 'akeeba');
		$session->set('transfer.ftpPassword', $ftpPassword, 'akeeba');
		$session->set('transfer.ftpPubKey', $ftpPubKey, 'akeeba');
		$session->set('transfer.ftpPrivateKey', $ftpPrivateKey, 'akeeba');
		$session->set('transfer.ftpDirectory', $ftpDirectory, 'akeeba');
		$session->set('transfer.ftpPassive', $ftpPassive ? 1 : 0, 'akeeba');

		/** @var \Akeeba\Backup\Admin\Model\Transfer $model */
		$model = $this->getModel();

		try
		{
			$config = $model->getFtpConfig();
			$model->testConnection($config);
		}
		catch (Exception $e)
		{
			$result = (object)[
				'status'    => false,
				'message'   => $e->getMessage(),
			];
		}

		@ob_end_clean();
		echo '###' . json_encode($result) . '###';
		$this->container->platform->closeApplication();
	}

	/**
	 * Initialise the upload: sends Kickstart and our add-on script to the remote server
	 *
	 * @return  void
	 */
	public function initialiseUpload()
	{
		$result = (object)[
			'status'    => true,
			'message'   => '',
		];

		/** @var \Akeeba\Backup\Admin\Model\Transfer $model */
		$model = $this->getModel();

		try
		{
			$config = $model->getFtpConfig();
			$model->initialiseUpload($config);
		}
		catch (Exception $e)
		{
			$result = (object)[
				'status'    => false,
				'message'   => $e->getMessage(),
			];
		}

		@ob_end_clean();
		echo '###' . json_encode($result) . '###';
		$this->container->platform->closeApplication();
	}

	/**
	 * Perform an upload step. Pass start=1 to reset the upload and start over.
	 *
	 * @return  void
	 */
	public function upload()
	{
		/** @var \Akeeba\Backup\Admin\Model\Transfer $model */
		$model = $this->getModel();

		if ($this->input->getBool('start', false))
		{
			$model->resetUpload();
		}

		try
		{
			$config = $model->getFtpConfig();
			$uploadResult = $model->uploadChunk($config);
		}
		catch (Exception $e)
		{
			$uploadResult = (object)[
				'status'    => false,
				'message'   => $e->getMessage(),
				'totalSize' => 0,
				'doneSize'  => 0,
				'done'      => false
			];
		}

		$result = (object)$uploadResult;

		@ob_end_clean();
		echo '###' . json_encode($result) . '###';
		$this->container->platform->closeApplication();
	}
}