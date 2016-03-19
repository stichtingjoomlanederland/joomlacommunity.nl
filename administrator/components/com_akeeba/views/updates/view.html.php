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

class AkeebaViewUpdates extends F0FViewHtml
{
	/**
	 * Latest update information
	 *
	 * @var  array
	 */
	public $updateInfo = null;

	/**
	 * Human readable name of the component
	 *
	 * @var  string
	 */
	public $componentTitle = '';

	/**
	 * Currently installed version
	 *
	 * @var  string
	 */
	public $currentVersion = '0.0.0';

	/**
	 * Does the user have to enter a download ID before being allowed to update the software?
	 *
	 * @var  bool
	 */
	public $needsDownloadID = false;

	/**
	 * Does the user need to provide FTP credentials?
	 *
	 * @var  bool
	 */
	public $needsFTPCredentials = false;

	/**
	 * Runs on the overview (default) task
	 *
	 * @param   string|null $tpl Ignored
	 *
	 * @return  bool  True to let the view display
	 */
	public function onOverview($tpl = null)
	{
		$this->setLayout('overview');

		/** @var AkeebaModelUpdates $model */
		$model = $this->getModel();

		/** @var AkeebaModelCpanels $cpanelModel */
		$cpanelModel = $this->getModel('cpanel');

		$this->updateInfo          = $model->getUpdates(false);
		$this->componentTitle      = $model->getComponentDescription();
		$this->currentVersion      = $model->getVersion();
		$this->needsFTPCredentials = $model->needsFTPCredentials();
		$this->needsDownloadID     = $cpanelModel->needsDownloadID();

		return true;
	}

	/**
	 * Runs on the "startupdate" task
	 *
	 * @param   string|null $tpl Ignored
	 *
	 * @return  bool  True to let the view display
	 */
	public function onStartupdate($tpl = null)
	{
		$this->setLayout('startupdate');

		return true;
	}

	/**
	 * Runs on the "download" task
	 *
	 * @param   string|null $tpl Ignored
	 *
	 * @return  bool  True to let the view display
	 */
	public function onDownload($tpl = null)
	{
		$this->setLayout('download');

		return true;
	}

	/**
	 * Runs on the "extract" task
	 *
	 * @param   string|null $tpl Ignored
	 *
	 * @return  bool  True to let the view display
	 */
	public function onExtract($tpl = null)
	{
		$this->setLayout('extract');

		return true;
	}


	/**
	 * Runs on the "install" task
	 *
	 * @param   string|null $tpl Ignored
	 *
	 * @return  bool  True to let the view display
	 */
	public function onInstall($tpl = null)
	{
		$this->setLayout('install');

		return true;
	}
}