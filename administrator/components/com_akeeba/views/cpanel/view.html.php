<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

/**
 * Akeeba Backup Control Panel view class
 *
 */
class AkeebaViewCpanel extends F0FViewHtml
{
	/**
	 * Active backup profile ID
	 *
	 * @var   int
	 */
	public $profileid = 1;

	/**
	 * List of backup profiles, for use with JHtmlSelect
	 *
	 * @var   array
	 */
	public $profilelist = array();

	/**
	 * List of profiles to display as Quick Icons in the control panel page
	 *
	 * @var   array  Array of stdClass objects
	 */
	public $quickIconProfiles = array();

	/**
	 * The HTML for the backup status cell
	 *
	 * @var   string
	 */
	public $statuscell = '';

	/**
	 * HTML for the warnings (status details)
	 *
	 * @var   string
	 */
	public $detailscell = '';

	/**
	 * Details of the latest backup as HTML
	 *
	 * @var   string
	 */
	public $statscell = '';

	/**
	 * Do I have to ask the user to fix the permissions?
	 *
	 * @var   bool
	 */
	public $fixedpermissions = false;

	/**
	 * Do I have to ask the user to provide a Download ID?
	 *
	 * @var   bool
	 */
	public $needsdlid = false;

	/**
	 * Did a Core edition user provide a Download ID instead of installing Akeeba Backup Professional?
	 *
	 * @var   bool
	 */
	public $needscoredlidwarning = false;

	/**
	 * Our extension ID
	 *
	 * @var   int
	 */
	public $extension_id = 0;

	/**
	 * Should I have the browser ask for desktop notification permissions?
	 *
	 * @var   bool
	 */
	public $desktop_notifications = false;

	/**
	 * If anonymous statistics collection is enabled and we have to collect statistics this will include the HTML for
	 * the IFRAME that performs the anonymous stats collection.
	 *
	 * @var   string
	 */
	public $statsIframe = '';

	/**
	 * If front-end backup is enabled and the secret word has an issue (too insecure) we populate this variable
	 *
	 * @var  string
	 */
	public $frontEndSecretWordIssue = '';

	/**
	 * In case the existing Secret Word is insecure we generate a new one. This variable contains the new Secret Word.
	 *
	 * @var  string
	 */
	public $newSecretWord = '';

    /**
     * Is the mbstring extension installed and enabled? This is required by Joomla and Akeeba Backup to correctly work
     * 
     * @var bool
     */
    public $checkMbstring = true;

	protected function onBrowse($tpl = null)
    {
		// Used in F0F 2.0, where this actually works as expected
		$this->onAdd($tpl);
	}

	protected function onAdd($tpl = null)
	{
		/** @var AkeebaModelCpanels $model */
		$model = $this->getModel();

		$session = JFactory::getSession();

		// Load the helper classes
		$this->loadHelper('utils');
		$this->loadHelper('status');
		$statusHelper = AkeebaHelperStatus::getInstance();

		// Load the model
		if(!class_exists('AkeebaModelStatistics')) JLoader::import('models.statistics', JPATH_COMPONENT_ADMINISTRATOR);

		$statmodel = new AkeebaModelStatistics();

		$this->profileid         = $model->getProfileID();               // Active profile ID
		$this->profilelist       = $model->getProfilesList();            // List of available profiles
		$this->quickIconProfiles = $model->getQuickIconProfiles();
		$this->statuscell        = $statusHelper->getStatusCell();       // Backup status
		$this->detailscell       = $statusHelper->getQuirksCell();       // Details (warnings)
		$this->statscell         = $statmodel->getLatestBackupDetails();
		$this->fixedpermissions  = $model->fixMediaPermissions();        // Fix media/com_akeeba permissions
        $this->checkMbstring     = $model->checkMbstring();

		$this->needsdlid            = $model->needsDownloadID();
		$this->needscoredlidwarning = $model->mustWarnAboutDownloadIDInCore();
		$this->extension_id         = $model->getState('extension_id', 0, 'int');

		$this->frontEndSecretWordIssue = $model->getFrontendSecretWordError();
		$this->newSecretWord           = $session->get('newSecretWord', null, 'akeeba.cpanel');

		// Should I ask for permission to display desktop notifications?
		JLoader::import('joomla.application.component.helper');
		$this->desktop_notifications = \Akeeba\Engine\Util\Comconfig::getValue('desktop_notifications', '0') ? 1 : 0;

		$this->statsIframe = F0FModel::getTmpInstance('Stats', 'AkeebaModel')->collectStatistics(true);

		return $this->onDisplay($tpl);
	}
}