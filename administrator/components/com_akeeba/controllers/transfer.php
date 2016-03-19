<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 *
 * @since     4.4.0
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Platform;
use Akeeba\Engine\Factory;

class AkeebaControllerTransfer extends AkeebaControllerDefault
{
	/** @var   JSession   The session we're working with */
	protected $session = null;

	/** @var   array  The tasks this controller is allowed to use */
	private $allowedTasks = ['wizard', 'checkUrl', 'applyConnection', 'initialiseUpload', 'upload', 'reset'];

	/**
	 * Overridden constructor; lets us inject a different session
	 *
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		$this->session = isset($config['session']) ? $config['session'] : JFactory::getSession();

		parent::__construct($config);
	}

	/**
	 * Override execute() to only allow specific tasks to run.
	 *
	 * @param   string   $task  The task we are asked to run.
	 *
	 * @return  bool|null
	 * @throws  Exception
	 */
	public function execute($task)
	{
		if (!in_array($task, $this->allowedTasks))
		{
			$task = $this->allowedTasks[0];
		}

		return parent::execute($task);
	}

	/**
	 * Default task, shows the wizard interface
	 *
	 * @param   bool   $cachable   Is this a cacheable view?
	 * @param   array  $urlparams  URL parameters for caching. False to let FOF figure it out by itself.
	 */
	public function wizard($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);
	}

	/**
	 * Reset the wizard
	 *
	 * @return  void
	 */
	public function reset()
	{
		$session = $this->session;
		$session->set('transfer', null, 'akeeba');
		$session->set('transfer.url', null, 'akeeba');
		$session->set('transfer.url_status', null, 'akeeba');
		$session->set('transfer.ftpsupport', null, 'akeeba');

		/** @var AkeebaModelTransfers $model */
		$model = $this->getThisModel();
		$model->resetUpload();

		$this->setRedirect('index.php?option=com_akeeba&view=transfer');
	}

	/**
	 * Cleans and checks the validity of the new site's URL
	 */
	public function checkUrl()
	{
		$url = $this->input->get('url', '', 'raw');

		/** @var AkeebaModelTransfers $model */
		$model = $this->getThisModel([
			'savestate' => 1
		]);
		$result = $model->checkAndCleanUrl($url);

		$session = $this->session;
		$session->set('transfer.url', $result['url'], 'akeeba');
		$session->set('transfer.url_status', $result['status'], 'akeeba');

		@ob_end_clean();
		echo '###' . json_encode($result) . '###';
		JFactory::getApplication()->close();
	}

	/**
	 * Applies the FTP/SFTP connection information and makes some preliminary validation
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
		$session = $this->session;

		$session->set('transfer.transferOption', $transferOption, 'akeeba');
		$session->set('transfer.ftpHost', $ftpHost, 'akeeba');
		$session->set('transfer.ftpPort', $ftpPort, 'akeeba');
		$session->set('transfer.ftpUsername', $ftpUsername, 'akeeba');
		$session->set('transfer.ftpPassword', $ftpPassword, 'akeeba');
		$session->set('transfer.ftpPubKey', $ftpPubKey, 'akeeba');
		$session->set('transfer.ftpPrivateKey', $ftpPrivateKey, 'akeeba');
		$session->set('transfer.ftpDirectory', $ftpDirectory, 'akeeba');
		$session->set('transfer.ftpPassive', $ftpPassive ? 1 : 0, 'akeeba');

		/** @var AkeebaModelTransfers $model */
		$model = $this->getThisModel();

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
		JFactory::getApplication()->close();
	}

	/**
	 * Initialise the upload: sends Kickstart and our add-on script to the remote server
	 */
	public function initialiseUpload()
	{
		$result = (object)[
			'status'    => true,
			'message'   => '',
		];

		/** @var AkeebaModelTransfers $model */
		$model = $this->getThisModel();

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
		JFactory::getApplication()->close();
	}

	/**
	 * Perform an upload step. Pass start=1 to reset the upload and start over.
	 */
	public function upload()
	{
		/** @var AkeebaModelTransfers $model */
		$model = $this->getThisModel();

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
		JFactory::getApplication()->close();
	}
}