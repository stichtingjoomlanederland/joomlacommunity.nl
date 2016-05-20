<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 * @since     1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/**
 * The Configuration Editor controller class
 *
 */
class AkeebaControllerConfig extends AkeebaControllerDefault
{
	/**
	 * Alias the add task to the general display task
	 *
	 * @return false|void
	 */
	public function add()
	{
		$this->display(false);
	}

	/**
	 * Handle the apply task which saves settings and shows the editor again
	 */
	public function apply()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		// Get the var array from the request
		$data = $this->input->get('var', array(), 'array', 4);
		$data['akeeba.flag.confwiz'] = 1;

		/** @var AkeebaModelConfigs $model */
		$model = $this->getThisModel();
		$model->setState('engineconfig', $data);
		$model->saveEngineConfig();

		// Finally, save the profile description if it has changed
		$profileid = \Akeeba\Engine\Platform::getInstance()->get_active_profile();

		// Get profile name
		$profileRecord = F0FModel::getTmpInstance('Profiles','AkeebaModel')
								 ->setId($profileid)
								 ->getItem();
		$oldProfileName = $profileRecord->description;
		$oldQuickIcon = $profileRecord->quickicon;

		$profileName = $this->input->getString('profilename', null);
		$profileName = trim($profileName);

		$quickIconValue = $this->input->getCmd('quickicon', '');
		$quickIcon = !empty($quickIconValue);

		$mustSaveProfile = !empty($profileName) && ($profileName != $oldProfileName);
		$mustSaveProfile = $mustSaveProfile || ($quickIcon != $oldQuickIcon);

		if ($mustSaveProfile)
		{
			$profileRecord->save(array(
				'description' => $profileName,
				'quickicon'   => $quickIcon
			));
		}

		$this->setRedirect(JUri::base() . 'index.php?option=com_akeeba&view=config', JText::_('COM_AKEEBA_CONFIG_SAVE_OK'));
	}

	/**
	 * Handle the save task which saves settings and returns to the cpanel
	 *
	 */
	public function save()
	{
		$this->apply();
		$this->setRedirect(JUri::base() . 'index.php?option=com_akeeba', JText::_('COM_AKEEBA_CONFIG_SAVE_OK'));
	}

	/**
	 * Handle the save task which saves settings, creates a new backup profile, activates it and proceed to the
	 * configuration page once more.
	 *
	 */
	public function savenew()
	{
		// Save the current profile
		$this->apply();

		// Create a new profile
		/** @var AkeebaModelProfiles $profileModel */
		$profileModel = F0FModel::getTmpInstance('Profiles', 'AkeebaModel');
		/** @var AkeebaTableProfile $profileTable */
		$profileTable = $profileModel->getTable();
		$profileid = \Akeeba\Engine\Platform::getInstance()->get_active_profile();
		$profileTable->load($profileid);
		$profileTable->id = null;
		$profileTable->save(array(
			'id' => 0,
			'description' => JText::_('COM_AKEEBA_CONFIG_SAVENEW_DEFAULT_PROFILE_NAME')
		));
		$newProfileId = (int)($profileTable->getId());

		// Activate and edit the new profile
		$returnUrl = base64_encode($this->redirect);
		$token = JFactory::getSession()->getFormToken();
		$url = JUri::base() . 'index.php?option=com_akeeba&task=switchprofile&profileid=' . $newProfileId .
			'&returnurl=' . $returnUrl . '&' . $token . '=1';
		$this->setRedirect($url);
	}

	/**
	 * Handle the cancel task which doesn't save anything and returns to the cpanel
	 */
	public function cancel()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$this->setRedirect(JUri::base() . 'index.php?option=com_akeeba');
	}

	/**
	 * Tests the validity of the FTP connection details
	 */
	public function testftp()
	{
		/** @var AkeebaModelConfigs $model */
		$model = $this->getThisModel();
		$model->setState('host', $this->input->get('host', '', 'raw', 2));
		$model->setState('port', $this->input->get('port', 21, 'int'));
		$model->setState('user', $this->input->get('user', '', 'raw', 2));
		$model->setState('pass', $this->input->get('pass', '', 'raw', 2));
		$model->setState('initdir', $this->input->get('initdir', '', 'raw', 2));
		$model->setState('usessl', $this->input->get('usessl', '', 'raw', 2) == 'true');
		$model->setState('passive', $this->input->get('passive', '', 'raw', 2) == 'true');

		@ob_end_clean();
		echo '###' . json_encode($model->testFTP()) . '###';
		flush();
		JFactory::getApplication()->close();
	}

	/**
	 * Tests the validity of the SFTP connection details
	 */
	public function testsftp()
	{
		/** @var AkeebaModelConfigs $model */
		$model = $this->getThisModel();
		$model->setState('host', $this->input->get('host', '', 'raw', 2));
		$model->setState('port', $this->input->get('port', 21, 'int'));
		$model->setState('user', $this->input->get('user', '', 'raw', 2));
		$model->setState('pass', $this->input->get('pass', '', 'raw', 2));
		$model->setState('privkey', $this->input->get('privkey', '', 'raw', 2));
		$model->setState('pubkey', $this->input->get('pubkey', '', 'raw', 2));
		$model->setState('initdir', $this->input->get('initdir', '', 'raw', 2));

		@ob_end_clean();
		echo '###' . json_encode($model->testSFTP()) . '###';
		flush();
		JFactory::getApplication()->close();
	}

	/**
	 * Opens an OAuth window for the selected data processing engine
	 */
	public function dpeoauthopen()
	{
		/** @var AkeebaModelConfigs $model */
		$model = $this->getThisModel();
		$model->setState('engine', $this->input->get('engine', '', 'raw'));
		$model->setState('params', $this->input->get('params', array(), 'array', 2));

		@ob_end_clean();
		$model->dpeOuthOpen();
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * Runs a custom API call against the selected data processing engine
	 */
	public function dpecustomapi()
	{
		/** @var AkeebaModelConfigs $model */
		$model = $this->getThisModel();
		$model->setState('engine', $this->input->get('engine', '', 'raw', 2));
		$model->setState('method', $this->input->get('method', '', 'raw', 2));
		$model->setState('params', $this->input->get('params', array(), 'array', 2));

		@ob_end_clean();
		echo '###' . json_encode($model->dpeCustomAPICall()) . '###';
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * Runs a custom API call against the selected data processing engine
	 */
	public function dpecustomapiraw()
	{
		/** @var AkeebaModelConfigs $model */
		$model = $this->getThisModel();
		$model->setState('engine', $this->input->get('engine', '', 'raw', 2));
		$model->setState('method', $this->input->get('method', '', 'raw', 2));
		$model->setState('params', $this->input->get('params', array(), 'array', 2));

		@ob_end_clean();
		echo $model->dpeCustomAPICall();
		flush();

		JFactory::getApplication()->close();
	}
}