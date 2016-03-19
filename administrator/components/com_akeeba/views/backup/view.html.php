<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 * @since     1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class AkeebaViewBackup extends F0FViewHtml
{
	/**
	 * This mess of a code is probably not one of my highlights in my code
	 * writing career. It's logically organized, badly architectured but I can
	 * still maintain it - and it works!
	 */
	public function onAdd($tpl = null)
	{
		AkeebaStrapper::addJSfile('media://com_akeeba/js/backup.js');

		/** @var AkeebaModelBackups $model */
		$model = $this->getModel();

		// Load the Status Helper
		if (!class_exists('AkeebaHelperStatus'))
		{
			JLoader::import('helpers.status', JPATH_COMPONENT_ADMINISTRATOR);
		}
		$helper = AkeebaHelperStatus::getInstance();

		// Determine default description
		JLoader::import('joomla.utilities.date');
		$jregistry           = JFactory::getConfig();
		$tzDefault           = $jregistry->get('offset');
		$user                = JFactory::getUser();
		$tz                  = $user->getParam('timezone', $tzDefault);
		$dateNow             = new JDate('now', $tz);
		$default_description =
			JText::_('BACKUP_DEFAULT_DESCRIPTION') . ' ' . $dateNow->format(JText::_('DATE_FORMAT_LC2'), true);
		$default_description = AkeebaHelperEscape::escapeJS($default_description, "'");

		$backup_description = $model->getState('description', $default_description);
		$comment            = $model->getState('comment', '');

		// Get a potential return URL
		$returnurl = $model->getState('returnurl');

		if (empty($returnurl))
		{
			$returnurl = '';
		}

		// Only allow internal URLs for the redirection
		if (!JUri::isInternal($returnurl))
		{
			$returnurl = '';
		}

		// If a return URL is set *and* the profile's name is "Site Transfer
		// Wizard", we are running the Site Transfer Wizard
		if (!class_exists('AkeebaModelProfiles'))
		{
			JLoader::import('models.profiles', JPATH_COMPONENT_ADMINISTRATOR);
		}

		/** @var AkeebaModelCpanels $cpanelmodel */
		$cpanelmodel  = F0FModel::getAnInstance('Cpanels', 'AkeebaModel');
		$profilemodel = new AkeebaModelProfiles();
		$profilemodel->setId($cpanelmodel->getProfileID());
		$profile_data = $profilemodel->getProfile();

		// Get the domain details from scripting facility
		$registry  = Factory::getConfiguration();
		$tag       = $model->getState('tag');
		$script    = $registry->get('akeeba.basic.backup_type', 'full');
		$scripting = Factory::getEngineParamsProvider()->loadScripting();
		$domains   = array();
		if (!empty($scripting))
		{
			foreach ($scripting['scripts'][ $script ]['chain'] as $domain)
			{
				$description = JText::_($scripting['domains'][ $domain ]['text']);
				$domain_key  = $scripting['domains'][ $domain ]['domain'];
				$domains[]   = array($domain_key, $description);
			}
		}
		$json_domains = AkeebaHelperEscape::escapeJS(json_encode($domains), '"\\');

		// Get the maximum execution time and bias
		$maxexec = $registry->get('akeeba.tuning.max_exec_time', 14) * 1000;
		$bias    = $registry->get('akeeba.tuning.run_time_bias', 75);

		// Check if the output directory is writable
		$quirks           = Factory::getConfigurationChecks()->getDetailedStatus();
		$unwritableOutput = array_key_exists('001', $quirks);

		// Pass on data
		$this->haserrors        = !$helper->status;
		$this->hasquirks        = $helper->hasQuirks();
		$this->quirks           = $helper->getQuirksCell(!$helper->status);
		$this->description      = $backup_description;
		$this->default_descr    = $default_description;
		$this->comment          = $comment;
		$this->domains          = $json_domains;
		$this->maxexec          = $maxexec;
		$this->bias             = $bias;
		$this->useiframe        = $registry->get('akeeba.basic.useiframe', 0) ? 'true' : 'false';
		$this->returnurl        = $returnurl;
		$this->unwritableoutput = $unwritableOutput;

		if ($registry->get('akeeba.advanced.archiver_engine', 'jpa') == 'jps')
		{
			$this->showjpskey = 1;
			$this->jpskey     = $registry->get('engine.archiver.jps.key', '');
		}
		else
		{
			$this->showjpskey = 0;
		}

		if (AKEEBA_PRO)
		{
			$this->showangiekey = 1;
			$this->angiekey     = $registry->get('engine.installer.angie.key', '');
		}
		else
		{
			$this->showangiekey = 0;
			$this->angiekey     = '';
		}
		$this->autostart = $model->getState('autostart');

		// Pass on profile info
		$this->profileid   = $cpanelmodel->getProfileID(); // Active profile ID
		$this->profilelist = $cpanelmodel->getProfilesList(); // List of available profiles

		// Should I ask for permission to display desktop notifications?
		JLoader::import('joomla.application.component.helper');
		$this->desktop_notifications = \Akeeba\Engine\Util\Comconfig::getValue('desktop_notifications', '0') ? 1 : 0;

		// Set the toolbar title
		$subtitle = JText::_('BACKUP');
		JToolBarHelper::title(JText::_('AKEEBA') . ':: <small>' . $subtitle . '</small>', 'akeeba');

		return true;
	}
}