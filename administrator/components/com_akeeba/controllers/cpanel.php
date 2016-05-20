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

/**
 * The Control Panel controller class
 *
 */
class AkeebaControllerCpanel extends F0FController
{
	public function execute($task)
	{
		if (!in_array($task, array('switchprofile', 'disablephpwarning', 'updateinfo', 'applydlid', 'resetSecretWord')))
		{
			$task = 'browse';
		}
		parent::execute($task);
	}

	public function onBeforeBrowse()
	{
		$result = parent::onBeforeBrowse();

		if ($result)
		{
			$model  = $this->getThisModel();
			$view   = $this->getThisView();

			/** @var AkeebaModelCpanels $model */

			$view->setModel($model);

			$aeconfig = Factory::getConfiguration();

			// Invalidate stale backups
			if (!class_exists('AkeebaHelperParams'))
			{
				require_once JPATH_ADMINISTRATOR . '/components/com_akeeba/helpers/params.php';
			}

			$params = new AkeebaHelperParams();

			Factory::resetState(array(
				'global' => true,
				'log'    => false,
				'maxrun' => $params->get('failure_timeout', 180)
			));

			// Just in case the reset() loaded a stale configuration...
			Platform::getInstance()->load_configuration();

			// Let's make sure the temporary and output directories are set correctly and writable...
			/** @var AkeebaModelConfwiz $wizmodel */
			$wizmodel = F0FModel::getAnInstance('Confwiz', 'AkeebaModel');
			$wizmodel->autofixDirectories();

			// Check if we need to toggle the settings encryption feature
			$model->checkSettingsEncryption();
			// Update the magic component parameters
			$model->updateMagicParameters();
			// Run the automatic database check
			$model->checkAndFixDatabase();

			// Run the automatic update site refresh
			/** @var AkeebaModelUpdates $updateModel */
			$updateModel = F0FModel::getTmpInstance('Updates', 'AkeebaModel');
			$updateModel->refreshUpdateSite();
		}

		return $result;
	}

	public function switchprofile()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$newProfile = $this->input->get('profileid', -10, 'int');

		if (!is_numeric($newProfile) || ($newProfile <= 0))
		{
			$this->setRedirect(JUri::base() . 'index.php?option=com_akeeba', JText::_('COM_AKEEBA_CPANEL_PROFILE_SWITCH_ERROR'), 'error');

			return true;
		}

		$session = JFactory::getSession();
		$session->set('profile', $newProfile, 'akeeba');
		$url       = '';
		$returnurl = $this->input->get('returnurl', '', 'base64');
		if (!empty($returnurl))
		{
			$url = base64_decode($returnurl);
		}
		if (empty($url))
		{
			$url = JUri::base() . 'index.php?option=com_akeeba';
		}
		$this->setRedirect($url, JText::_('COM_AKEEBA_CPANEL_PROFILE_SWITCH_OK'));
	}

	public function disablephpwarning()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		// Fetch the component parameters
		$db  = F0FPlatform::getInstance()->getDbo();
		$sql = $db->getQuery(true)
				  ->select($db->qn('params'))
				  ->from($db->qn('#__extensions'))
				  ->where($db->qn('type') . ' = ' . $db->q('component'))
				  ->where($db->qn('element') . ' = ' . $db->q('com_akeeba'));
		$db->setQuery($sql);
		$rawparams = $db->loadResult();
		$params    = new JRegistry();
		$params->loadString($rawparams, 'JSON');

		// Set the displayphpwarning parameter to 0
		$params->set('displayphpwarning', 0);

		// Save the component parameters
		$data = $params->toString('JSON');
		$sql  = $db->getQuery(true)
				   ->update($db->qn('#__extensions'))
				   ->set($db->qn('params') . ' = ' . $db->q($data))
				   ->where($db->qn('type') . ' = ' . $db->q('component'))
				   ->where($db->qn('element') . ' = ' . $db->q('com_akeeba'));

		$db->setQuery($sql);
		$db->execute();

		// Redirect back to the control panel
		$url       = '';
		$returnurl = $this->input->get('returnurl', '', 'base64');
		if (!empty($returnurl))
		{
			$url = base64_decode($returnurl);
		}
		if (empty($url))
		{
			$url = JUri::base() . 'index.php?option=com_akeeba';
		}
		$this->setRedirect($url);
	}

	public function updateinfo()
	{
		$result = '';

		/** @var AkeebaModelUpdates $updateModel */
		$updateModel = F0FModel::getTmpInstance('Updates', 'AkeebaModel');
		$infoArray   = $updateModel->getUpdates();
		$updateInfo  = (object)$infoArray;

		$result = '';

		$updateMethod = $updateModel->getUpdateMethod();

		switch ($updateMethod)
		{
			case 'joomla':
				$updateUrl = 'index.php?option=com_installer&view=update';
				break;

			default:
			case 'classic':
				$updateUrl = 'index.php?option=com_akeeba&view=update';
				break;
		}

		if ($updateInfo->hasUpdate)
		{
			$strings = array(
					'header'  => JText::sprintf('COM_AKEEBA_CPANEL_MSG_UPDATEFOUND', $updateInfo->version),
					'button'  => JText::sprintf('COM_AKEEBA_CPANEL_MSG_UPDATENOW', $updateInfo->version),
					'infourl' => $updateInfo->infoURL,
					'infolbl' => JText::_('COM_AKEEBA_CPANEL_MSG_MOREINFO'),
			);

			$result = <<<ENDRESULT
	<div class="alert alert-warning">
		<h3>
			<span class="icon icon-exclamation-sign glyphicon glyphicon-exclamation-sign"></span>
			{$strings['header']}
		</h3>
		<p>
			<a href="$updateUrl" class="btn btn-primary">
				{$strings['button']}
			</a>
			<a href="{$strings['infourl']}" target="_blank" class="btn btn-small btn-info">
				{$strings['infolbl']}
			</a>
		</p>
	</div>
ENDRESULT;
		}

		echo '###' . $result . '###';

		// Cut the execution short
		JFactory::getApplication()->close();
	}

	/**
	 * Applies the Download ID when the user is prompted about it in the Control Panel
	 */
	public function applydlid()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$msg = JText::_('COM_AKEEBA_CPANEL_ERR_INVALIDDOWNLOADID');
		$msgType = 'error';
		$dlid = $this->input->getString('dlid', '');

		// If the Download ID seems legit let's apply it
		if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
		{
			$msg = null;
			$msgType = null;
			
			if (!class_exists('AkeebaHelperParams'))
			{
				require_once JPATH_ADMINISTRATOR . '/components/com_akeeba/helpers/params.php';
			}

			$params = new AkeebaHelperParams();
			$params->set('update_dlid', $dlid);
			$params->save();
		}

		// Redirect back to the control panel
		$url       = '';
		$returnurl = $this->input->get('returnurl', '', 'base64');
		if (!empty($returnurl))
		{
			$url = base64_decode($returnurl);
		}
		if (empty($url))
		{
			$url = JUri::base() . 'index.php?option=com_akeeba';
		}
		$this->setRedirect($url, $msg, $msgType);
	}

	/**
	 * Reset the Secret Word for front-end and remote backup
	 *
	 * @return  void
	 */
	public function resetSecretWord()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		if (!class_exists('AkeebaHelperParams'))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_akeeba/helpers/params.php';
		}

		$params = new AkeebaHelperParams();

		$session = JFactory::getSession();
		$newSecret = $session->get('newSecretWord', null, 'akeeba.cpanel');

		if (empty($newSecret))
		{
			$random = new \Akeeba\Engine\Util\RandomValue();
			$newSecret = $random->generateString(32);
			$session->set('newSecretWord', $newSecret, 'akeeba.cpanel');
		}

		$params->set('frontend_secret_word', $newSecret);
		$params->save();

		$msg = JText::sprintf('COM_AKEEBA_CPANEL_MSG_FESECRETWORD_RESET', $newSecret);

		$url = 'index.php?option=com_akeeba';
		$this->setRedirect($url, $msg);
	}
}